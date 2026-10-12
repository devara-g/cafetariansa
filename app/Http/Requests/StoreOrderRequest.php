<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:25'],
            'customer_type' => ['required', 'string', 'in:student,teacher,staff,guest'],
            'pickup_type' => ['required', 'string', 'in:pickup_at_stand,deliver_to_room'],
            'delivery_location' => ['nullable', 'string', 'max:100', 'required_if:pickup_type,deliver_to_room'],
            'pickup_time_slot' => ['required', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'string', 'in:qris,gopay,shopeepay,bca_va,bri_va,mandiri_va,cash'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'items.*.notes' => ['nullable', 'string', 'max:200'],
        ];
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_name' => 'Nama Pemesan',
            'customer_phone' => 'Nomor WhatsApp',
            'customer_type' => 'Tipe Pemesan',
            'pickup_type' => 'Metode Pengambilan',
            'delivery_location' => 'Lokasi Pengantaran',
            'pickup_time_slot' => 'Waktu Pengambilan',
            'payment_method' => 'Metode Pembayaran',
            'items' => 'Menu Pesanan',
            'items.*.menu_id' => 'Menu',
            'items.*.quantity' => 'Jumlah Porsi',
        ];
    }
}
