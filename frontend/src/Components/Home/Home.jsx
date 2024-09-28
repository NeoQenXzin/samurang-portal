import React, { useState, useEffect } from 'react';
import axios from 'axios';

function Home() {
  const [user, setUser] = useState(null);

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const response = await axios.get('localhost:8000/api/dojangs/1', {
          headers: {
            
          }
        });
        setUser(response.data);
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    };
   
    fetchUser();
  }, []);

  if (!user) return <div>Loading...</div>;

  return (
    <div>
      {/* <h1>Welcome, {user.firstName} {user.lastName}</h1>
      <p>Grade: {user.grade}</p> */}
      <p>Dojang: {user.name}</p>
    </div>
  );
}

export default Home;