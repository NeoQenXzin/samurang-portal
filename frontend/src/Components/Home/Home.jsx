import React, { useState, useEffect } from 'react';
import axios from 'axios';

function Home() {
  const [dojang, setDojang] = useState(null);

  useEffect(() => {
    const fetchDojang = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/dojangs/1', {
          headers: {
            'Accept': 'application/json'
          }
        });
        setDojang(response.data);
      } catch (error) {
        console.error('Error fetching dojang data:', error);
      }
    };
   
    fetchDojang();
  }, []);

  if (!dojang) return <div>Loading...</div>;

  return (
    <div>
      <h1>Dojang Information</h1>
      <p>Name: {dojang.name}</p>
      <p>City: {dojang.city}</p>
    </div>
  );
}

export default Home;