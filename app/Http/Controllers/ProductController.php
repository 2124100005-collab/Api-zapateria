<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private function products(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Chikarawas Runner Pro',
                'description' => 'Tenis de running de alto rendimiento con suela de goma reforzada y plantilla ergonómica. Ideal para largas distancias y terrenos mixtos.',
                'price' => 1499.00,
                'stock' => 25,
                'category' => 'Running',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80',
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&h=400&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=600&fit=crop&q=80',
                ],
            ],
            [
                'id' => 2,
                'name' => 'Chikarawas Street Flow',
                'description' => 'Tenis urbanos con estilo casual deportivo. Parte superior de malla transpirable y suela antideslizante. Perfectos para el día a día.',
                'price' => 1199.00,
                'stock' => 40,
                'category' => 'Casual',
                'image' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600&q=80',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600&h=400&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400&h=600&fit=crop&q=80',
                ],
            ],
            [
                'id' => 3,
                'name' => 'Chikarawas Boost X',
                'description' => 'Tenis de entrenamiento con tecnología de amortiguación avanzada. Soporte lateral reforzado para máximo control en gym y crossfit.',
                'price' => 1799.00,
                'stock' => 15,
                'category' => 'Training',
                'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&q=80',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&h=400&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=400&h=600&fit=crop&q=80',
                ],
            ],
            [
                'id' => 4,
                'name' => 'Chikarawas Air Glide',
                'description' => 'Tenis ligeros con cámara de aire en la suela. Diseño moderno con colores vibrantes para quienes buscan estilo y comodidad.',
                'price' => 2199.00,
                'stock' => 10,
                'category' => 'Lifestyle',
                'image' => 'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600&q=80',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600&h=400&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=400&h=600&fit=crop&q=80',
                ],
            ],
            [
                'id' => 5,
                'name' => 'Chikarawas Trail Force',
                'description' => 'Tenis para trail running con agarre extremo. Puntera reforzada y membrana impermeable para tus aventuras al aire libre.',
                'price' => 2499.00,
                'stock' => 20,
                'category' => 'Trail',
                'image' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&q=80',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&h=400&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=400&h=600&fit=crop&q=80',
                ],
            ],
            [
                'id' => 6,
                'name' => 'Chikarawas Classic Retro',
                'description' => 'Tenis de diseño retro inspirados en los clásicos de los 90s. Cuero sintético de alta calidad con suela vulcanizada.',
                'price' => 999.00,
                'stock' => 35,
                'category' => 'Retro',
                'image' => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=600&q=80',
'images' => [
    'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=600&q=80',
    'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=600&h=400&fit=crop&q=80',
    'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400&h=600&fit=crop&q=80',
],
            ],
        ];
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->products(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = collect($this->products())->firstWhere('id', $id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }
}