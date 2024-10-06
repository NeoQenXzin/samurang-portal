import React, { useState, useEffect } from "react";
import axios from "axios";

function HomePage() {
  const [instructor, setInstructor] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchInstructorData = async () => {
      try {
        const token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MjgyMjA0ODgsImV4cCI6MTcyODMwNjg4OCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InF1ZW50aW4ucGVjaGFycm9tYW4xM0BnbWFpbC5jb20ifQ.Vj0LnAk-ambHeINDEITDlgvVT-vmpRXTxcHgOR9OR5f2DzMGUgwK8AvgVWEpvpyJTzAdYg3EkEzGhATTmHqkAaBSeEOghI8mJMKhO6Ra4fFOmP7pPlIJ5uOz2G2Wz_oYMr92Gmeadyhu8ax1wvliP7veQKvSw1BSCxcgPnbIhdnt8X4KC6xv6mC6sclQEKrBAUxuD4WnuphyAWpvgj4gUV-7yJWv1dOFtEnD0GOvsN2wnQs5TNj7Ww-M2f_IcexOpN6EKb8oMwpNQvAv8GP1Zv6_xZeAdDVIuVbKFlXesEQYgagyvv27l2RnwhK2DQwHuCGsNGLeb7KESUk3lzqK2g";
        const response = await axios.get(
          "http://localhost:8000/api/instructors/1",
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        );
        setInstructor(response.data);
        setLoading(false);
      } catch (err) {
        setError("Erreur lors du chargement des données");
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
      <h1>
        Bonjour {instructor.firstname} {instructor.lastname}
      </h1>
      <p>Vous êtes du dojang : {instructor.dojang.name}</p>
      <p>Nombre d'élèves : {instructor.students.length}</p>
      <p>Votre grade est : {instructor.grade.name}</p>
      {/* Ajoutez d'autres informations selon vos besoins */}
    </div>
  );
}

export default HomePage;
