import React, { useState, useEffect } from 'react';
import axios from 'axios';

function HomePage() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchUserData = async () => {
      const urlParams = new URLSearchParams(window.location.search);
      const token = urlParams.get('token');
      
      if (!token) {
        setError('Aucun token trouvé');
        setLoading(false);
        return;
      }

      try {
        const response = await axios.get('http://localhost:8000/api/current_user', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        setUser(response.data);
        setLoading(false);
      } catch (err) {
        setError('Erreur lors du chargement des données ');
        setLoading(false);
      }
    };

    fetchUserData();
  }, []);

  if (loading) return <div>Chargement...</div>;
  if (error) return <div>{error}</div>;
  if (!user) return <div>Aucune donnée utilisateur trouvée</div>;

  return (
    <div>
      <h1>Bonjour {user.firstname} {user.lastname}</h1>
      <p>Votre rôle : {user.roles.includes('ROLE_ADMIN') ? 'Administrateur' : 
                       user.roles.includes('ROLE_INSTRUCTOR') ? 'Instructeur' : 'Étudiant'}</p>
      <p>Dojang : {user.dojang?.name}</p>
      <p>Grade : {user.grade?.name}</p>
      {user.roles.includes('ROLE_INSTRUCTOR') && (
        <p>Nombre d'élèves : {user.students?.length}</p>
      )}
    </div>
  );
}

export default HomePage;