<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-md p-6">
        <h1 class="text-2xl font-semibold mb-6">Products</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2">Unique Key</th>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">Style#</th>
                        <th class="px-4 py-2">Color</th>
                        <th class="px-4 py-2">Size</th>
                        <th class="px-4 py-2">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-4 py-2">{{ $product->unique_key }}</td>
                            <td class="px-4 py-2">{{ $product->product_title }}</td>
                            <td class="px-4 py-2">{{ $product->product_description }}</td>
                            <td class="px-4 py-2">{{ $product->style }}</td>
                            <td class="px-4 py-2">{{ $product->sanmar_mainframe_color }} / {{ $product->color_name }}
                            </td>
                            <td class="px-4 py-2">{{ $product->size }}</td>
                            <td class="px-4 py-2">${{ number_format($product->piece_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</body>

</html>
