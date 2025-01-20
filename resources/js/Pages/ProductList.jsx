
import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import { InertiaLink } from '@inertiajs/inertia-react';
import Layout from '../Layouts/Authenticated';

const ProductList = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    axios.get('/api/products')
      .then(response => {
        setProducts(response.data);
      })
      .catch(err => {
        setError(err.response?.data?.message || 'Failed to fetch products');
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  if (loading) {
    return <p className="text-center mt-8 text-lg">Loading products...</p>;
  }

  if (error) {
    return <p className="text-center mt-8 text-lg text-red-500">{error}</p>;
  }

  return (
    <Layout>
      <div className="container mx-auto px-4 py-8">
        <h2 className="text-3xl font-bold mb-6 text-center">Our Products</h2>
        <div className="flex justify-end mb-6">
          <InertiaLink href="/add-product" className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            Add New Product
          </InertiaLink>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {products.map(product => (
            <div key={product.id} className="bg-white shadow-md rounded-lg overflow-hidden m-4 max-w-sm">
              {product.image_path && (
                <img
                  className="w-full h-48 object-cover"
                  src={`http://localhost:8000/storage/${product.image_path}`}
                  alt={product.name}
                />
              )}
              <div className="p-4">
                <h3 className="text-xl font-semibold text-gray-800">{product.name}</h3>
                <p className="text-gray-600 mt-2">{product.description}</p>
                <div className="mt-4 flex justify-between items-center">
                  <span className="text-lg font-bold text-green-600">${product.price}</span>
                  <InertiaLink
                    href={`/products/${product.id}`}
                    className="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
                  >
                    View
                  </InertiaLink>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </Layout>
  );
};

export default ProductList;
