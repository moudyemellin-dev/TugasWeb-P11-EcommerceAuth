<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EcommerceSeeder extends Seeder
{
    public function run(): void
    {
        // USER MULTI-ROLE
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Ecommerce',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $editor = User::updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor Ecommerce',
                'password' => Hash::make('password'),
                'role' => 'editor',
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Ecommerce',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // KATEGORI
        $categoryNames = [
            'Elektronik',
            'Fashion',
            'Makanan',
            'Minuman',
            'Olahraga',
            'Kecantikan',
            'Peralatan Rumah',
            'Buku',
            'Aksesoris',
            'Komputer',
        ];

        foreach ($categoryNames as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => 'Kategori ' . $name,
                ]
            );
        }

        // TAG
        $tagNames = [
            'Terbaru',
            'Populer',
            'Promo',
            'Murah',
            'Premium',
            'Best Seller',
            'Rekomendasi',
            'Diskon',
            'Favorit',
            'Limited',
        ];

        foreach ($tagNames as $name) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // MINIMAL 60 PRODUK
        $jumlahProduk = Product::count();

        if ($jumlahProduk < 60) {
            Product::factory()
                ->count(60 - $jumlahProduk)
                ->state(function () use ($admin) {
                    return [
                        'category_id' => Category::inRandomOrder()->value('id'),
                        'user_id' => $admin->id,
                    ];
                })
                ->create();
        }

        // HUBUNGKAN PRODUK DENGAN TAG
        $tagIds = Tag::pluck('id');

        Product::all()->each(function ($product) use ($tagIds) {
            $product->tags()->sync(
                $tagIds->random(2)->values()->all()
            );
        });

        // DATA ORDER
        $users = [$admin, $editor, $user];

        for ($i = 1; $i <= 6; $i++) {
            $orderUser = $users[($i - 1) % 3];

            $order = Order::updateOrCreate(
                ['order_number' => 'ORD-' . str_pad($i, 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $orderUser->id,
                    'total_amount' => 0,
                    'status' => 'paid',
                    'order_date' => now(),
                ]
            );

            $products = Product::inRandomOrder()->take(2)->get();

            foreach ($products as $product) {
                OrderItem::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'qty' => 1,
                        'price' => $product->price,
                    ]
                );
            }

            $order->update([
                'total_amount' => $order->items()
                    ->get()
                    ->sum(fn ($item) => $item->qty * $item->price),
            ]);
        }
    }
}
