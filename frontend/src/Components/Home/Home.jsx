import React, { useState, useEffect } from 'react';
import axios from 'axios';

function HomePage() {
  const [instructor, setInstructor] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchInstructorData = async () => {
      try {
        const token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3Mjc1OTQ5MDksImV4cCI6MTcyNzY4MTMwOSwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InF1ZW50aW4ucGVjaGFycm9tYW4xM0BnbWFpbC5jb20ifQ.UyObhw4iLzbamaWKKhpmiEdvmPkX8xJOKtXIp6E96SANir86sedqSmbzGDtj12CValJtyKHOJKV5ZeJOWh8C7s9MuLAYD5J9YNF0FEFbcub_4ZbhlLggQ9GkbwUyCZFyPDPhxpDxWF3svcDBv_Q7DlNn-Y_MS8_Xa-1Jyur2UKjGtMzoOuN73_ShCP6ILxnBj9OeLkcDZc-gz8xh3RQgRQIhQi6CdCFybZICXvxGmh0pSJ7ufLDnW2y6HEdNdtspSSfLtyUbANir_knDSTGxzynxr0wabhPADmGCLeOKDMpOys23R1w3BPJp7trVpiA410M9GQEHmEBN3AG7yBXpqw'; // Assurez-vous de stocker le token après la connexion
        const response = await axios.get('http://localhost:8000/api/instructors/1', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        setInstructor(response.data);
        setLoading(false);
      } catch (err) {
        setError('Erreur lors du chargement des données');
        setLoading(false);
      }
    };

    fetchInstructorData();
  }, []);

  if (loading) return <div>Chargement...</div>;
  if (error) return <div>{error}</div>;
  if (!instructor) return <div>Aucune donnée d'instructeur trouvée</div>;

  return (
    <div>
      <h1>Bonjour {instructor.firstname} {instructor.lastname}</h1>
      <p>Vous êtes du dojang : {instructor.dojang.name}</p>
      <p>Nombre d'élèves : {instructor.students.length}</p>
      <p>Votre grade est : {instructor.grade.name}</p>
      {/* Ajoutez d'autres informations selon vos besoins */}
    </div>
  );
}

export default HomePage;