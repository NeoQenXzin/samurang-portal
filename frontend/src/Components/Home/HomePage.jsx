import React, { useState, useEffect } from "react";
import axios from "axios";

axios.defaults.withCredentials = true;

function HomePage() {
  const [user, setUser] = useState(null);
  const [nextOrder, setNextOrder] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchNextOrderData = async (token) => {
    try {
      const response = await axios.get(
        "http://localhost:8000/api/next_orders",
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      console.log("Next order response:", response.data);
      if (response.data.member && response.data.member.length > 0) {
        setNextOrder(response.data.member[0]);
        console.log("Next order set:", response.data.member[0]);
      } else {
        console.log("Aucune donnée de commande trouvée");
      }
    } catch (err) {
      console.error("Erreur lors du chargement des données de commande", err);
    }
  };

  useEffect(() => {
    const fetchUserData = async () => {
      let token = localStorage.getItem("token");
      const urlParams = new URLSearchParams(window.location.search);
      const urlToken = urlParams.get("token");

      if (urlToken) {
        token = urlToken;
        localStorage.setItem("token", token);
        window.history.replaceState({}, document.title, "/home");
      }

      if (!token) {
        setError("Aucun token trouvé");
        setLoading(false);
        return;
      }

      try {
        const userResponse = await axios.get(
          "http://localhost:8000/api/current_user",
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        );
        setUser(userResponse.data);
        await fetchNextOrderData(token);
        setLoading(false);
      } catch (err) {
        setError("Erreur lors du chargement des données");
        setLoading(false);
      }
    };

    fetchUserData();
  }, []);

  const handleLogout = async () => {
    try {
      const token = localStorage.getItem("token");
      await axios.post("http://localhost:8000/logout", null, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      localStorage.removeItem("token");
      window.location.href = "http://localhost:8000/login";
    } catch (error) {
      console.error("Erreur lors de la déconnexion", error);
    }
  };

  if (loading) return <div>Chargement...</div>;
  if (error) return <div>{error}</div>;
  if (!user) return <div>Aucune donnée utilisateur trouvée</div>;

  return (
    <div>
      <h1>
        Bonjour {user.firstname} {user.lastname}
      </h1>
      <p>
        Votre rôle :{" "}
        {user.roles.includes("ROLE_ADMIN")
          ? "Administrateur"
          : user.roles.includes("ROLE_INSTRUCTOR")
          ? "Instructeur"
          : "Étudiant"}
      </p>
      <p>Dojang : {user.dojang?.name}</p>
      <p>Grade : {user.grade?.name}</p>
      {user.roles.includes("ROLE_INSTRUCTOR") && (
        <p>Nombre d'élèves : {user.students?.length}</p>
      )}
      <div>
        <h2>Prochaine date de commande</h2>
        {nextOrder ? (
          <>
            <p>
              Date d'envoi : {new Date(nextOrder.sendDate).toLocaleDateString()}
            </p>
            <p>État : {nextOrder.status}</p>
          </>
        ) : (
          <p>Aucune information sur la prochaine commande disponible</p>
        )}
      </div>
      <button onClick={handleLogout}>Se déconnecter</button>
    </div>
  );
}

export default HomePage;