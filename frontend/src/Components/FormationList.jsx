import React, { useState, useEffect } from 'react';
import axios from 'axios';

function FormationsList() {
  const [formations, setFormations] = useState([]);
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchFormations();
    fetchCurrentUser();
  }, []);

  const fetchFormations = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/formations', {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
      });
      if (response.data.member && response.data.member.length > 0) {
        setFormations(response.data.member);
        console.log("formations", response.data.member[0]);
      } else {
        console.log("Aucune donnée de formation trouvée");
      }
    } catch (err) {
      setError('Erreur lors du chargement des formations');
    } finally {
      setLoading(false);
    }
  };

  const fetchCurrentUser = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/current_user', {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
      });
      setUser(response.data);
    } catch (err) {
      setError('Erreur lors du chargement des données utilisateur');
    }
  };

  const handleToggleParticipation = async (formationId) => {
    let isParticipating = null
    try {
      const formation = formations.find(f => f.id === formationId);
      if (!formation) {
        throw new Error('Formation non trouvée');
      }
      isParticipating = await isUserParticipating(formation);
      const endpoint = isParticipating ? 'unregister' : 'register';
      
      await axios.post(`http://localhost:8000/api/formations/${formationId}/${endpoint}`, {}, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
      });
      
      // Mettre à jour la formation localement
      const updatedFormations = formations.map(f => {
        if (f.id === formationId) {
          const updatedParticipants = isParticipating
            ? (f.instructorParticipants || []).filter(p => p.id !== user?.id)
            : [...(f.instructorParticipants || []), { id: user?.id, firstname: user?.firstname, lastname: user?.lastname }];
          return { ...f, 
            instructorParticipants: updatedParticipants,
            participantsCount: isParticipating ? f.participantsCount - 1 : f.participantsCount + 1};
        }
        return f;
      });
      
      setFormations(updatedFormations);
    } catch (err) {
      setError(`Erreur lors de ${isParticipating ? 'la désinscription' : 'l\'inscription'}: ${err.message}`);
    }
  };

  const isUserParticipating = (formation) => {
    return formation.instructorParticipants && 
           Array.isArray(formation.instructorParticipants) &&
           formation.instructorParticipants.some(p => p.id === user?.id);
  };

  if (loading) return <div>Chargement...</div>;
  if (error) return <div>{error}</div>;

  return (
    <div>
      <h1>Liste des Formations</h1>
      {formations.map(formation => (
        <div key={formation.id}>
          <h2>{formation.type}</h2>
          <p>Date: {new Date(formation.startDate).toLocaleDateString()} - {new Date(formation.endDate).toLocaleDateString()}</p>
          <p>Lieu: {formation.location}</p>
          <p>Nombre de participants: {formation.participantsCount || 0}</p>
          {isUserParticipating(formation) && <p>Vous participez à cet événement</p>}
          <button onClick={() => handleToggleParticipation(formation.id)}>
            {isUserParticipating(formation) ? 'Se désinscrire' : 'S\'inscrire'}
          </button>
          <h3>Participants:</h3>
          <ul>
            {formation.instructorParticipants && formation.instructorParticipants.map(instructor => (
              <li key={instructor.id}>{instructor.firstname} {instructor.lastname} (Instructeur)</li>
            ))}
            {formation.studentParticipants && formation.studentParticipants.map(student => (
              <li key={student.id}>{student.firstname} {student.lastname} (Étudiant)</li>
            ))}
          </ul>
        </div>
      ))}
    </div>
  );
}

export default FormationsList;