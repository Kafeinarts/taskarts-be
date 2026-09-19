<?php

namespace App\Modules\Settings\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'key', 'value', 'type', 'group'])]
class Setting extends Model
{
    use HasFactory;

    /**
     * Relasi: pemilik pengaturan ini (jika pengaturan bersifat per-user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ambil nilai pengaturan dalam bentuk yang sudah dikonversi sesuai tipe datanya.
     *
     * Tipe yang dikenali: string, integer, float, boolean, json, array.
     */
    public function getValueParsedAttribute(): mixed
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'float', 'decimal' => (float) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array' => json_decode((string) $this->value, true),
            default => $this->value,
        };
    }

    /**
     * Simpan nilai pengaturan sambil menormalkan tipe data secara otomatis.
     */
    public function setValueData(mixed $value): void
    {
        if (is_bool($value)) {
            $this->type = 'boolean';
            $this->value = $value ? '1' : '0';
        } elseif (is_int($value)) {
            $this->type = 'integer';
            $this->value = (string) $value;
        } elseif (is_array($value)) {
            $this->type = 'json';
            $this->value = json_encode($value);
        } else {
            $this->type = 'string';
            $this->value = (string) $value;
        }
    }
}
