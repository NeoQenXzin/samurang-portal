import React, { useState, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
  FaUserCircle,
  FaMapMarkerAlt,
  FaExternalLinkAlt,
} from "react-icons/fa";
import { fetchUserProfile } from "../../store/slices/profileSlice";
import dojoImage from "../../assets/icones/dojo.png";

export default function Profil() {
  const dispatch = useDispatch();
  const { userData, isLoading, error } = useSelector((state) => state.profile);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const userRole = userData.user.roles[0];

  useEffect(() => {
    dispatch(fetchUserProfile());
    console.log("userData:", userData);
    console.log("students:", students);
  }, [dispatch]);

  if (isLoading) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  if (error) {
    return <div className="text-center text-red-500 mt-4 p-4">{error}</div>;
  }

  const user = userData?.user;
  const dojang = user?.dojang;
  const students = userData?.students || [];

  const handleAdminAccess = () => {
    window.open("http://localhost:8000/mydojang", "_blank");
  };

  const handleSuperAdminAccess = () => {
    window.open("http://localhost:8000/admin", "_blank");
  };

  const handleContactAdmin = () => {
    window.location.href = "mailto:quentin.pecharroman13@gmail.com";
  };

  const UserModal = () => (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div className="bg-white p-6 rounded-lg max-w-2xl mx-auto">
        <h2 className="text-2xl font-bold mb-4">Informations complètes</h2>
        <div className="space-y-4">
          <div>
            <h3 className="font-semibold text-gray-700">
              Informations personnelles
            </h3>
            <p>
              Nom complet: {user?.firstname} {user?.lastname}
            </p>
            <p>Email: {user?.email}</p>
            <p>Grade: {user?.grade?.name}</p>
            <p>
              Date d'inscription:{" "}
              {new Date(user?.createdAt).toLocaleDateString("fr-FR")}
            </p>
          </div>
          <div>
            <h3 className="font-semibold text-gray-700">Dojang</h3>
            <p>Nom: {dojang?.name}</p>
            <p>Adresse: {dojang?.address}</p>
            <p>Ville: {dojang?.city}</p>
            <p>Code postal: {dojang?.postalCode}</p>
          </div>
        </div>
        <button
          onClick={() => setIsModalOpen(false)}
          className="mt-6 w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors"
        >
          Fermer
        </button>
      </div>
    </div>
  );

  return (
    <div className="bg-gray-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
      <div className="max-w-3xl mx-auto space-y-8">
        {/* Encadré des données utilisateur */}
        <div className="bg-white rounded-xl shadow-md p-6">
          <div className="flex items-center space-x-4">
            <FaUserCircle className="h-16 w-16 text-blue-600" />
            <div>
              <h1 className="text-3xl font-bold text-gray-900">
                {user?.firstname} {user?.lastname}
              </h1>
              <p className="text-sm text-gray-600 mt-1">{user?.email}</p>
            </div>
          </div>
          <div className="mt-6 space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Grade :</span>
              <span className="font-medium text-gray-900">
                {user?.grade?.name || "Non défini"}
              </span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Rôle :</span>
              <span className="font-medium text-gray-900">
                {user?.roles?.includes("ROLE_ADMIN")
                  ? "Administrateur"
                  : user?.roles?.includes("ROLE_INSTRUCTOR")
                  ? "Instructeur"
                  : "Étudiant"}
              </span>
            </div>
          </div>
        </div>

        {/* Vignette Dojang */}
        {dojang && (
          <div className="bg-white rounded-xl shadow-md p-6 flex items-center space-x-4">
            <img
              src={dojoImage}
              alt="Dojo"
              className="h-24 w-24 rounded-lg object-cover"
            />
            <div>
              <h2 className="text-xl font-semibold text-gray-900">
                {dojang.name}
              </h2>
              <p className="text-sm text-gray-600 mt-1 flex items-center">
                <FaMapMarkerAlt className="mr-2" />
                {`${dojang.address}, ${dojang.postalCode} ${dojang.city}` ||
                  "Adresse non disponible"}
              </p>
            </div>
          </div>
        )}

        {/* Vignette Élèves */}
        {user?.roles?.includes("ROLE_INSTRUCTOR") && students?.length > 0 && (
          <div className="bg-white rounded-xl shadow-md p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-xl font-semibold text-gray-900">
                Vos élèves
              </h2>
              <span className="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                {students.length} élèves
              </span>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              {students.map((student) => (
                <div
                  key={student.id}
                  className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                >
                  <div className="flex items-center space-x-3">
                    <div className="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                      <span className="text-blue-600 text-sm font-medium">
                        {student.firstname[0]}
                        {student.lastname[0]}
                      </span>
                    </div>
                    <span className="text-gray-700">
                      {student.firstname} {student.lastname}
                    </span>
                  </div>
                  <span className="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                    {student.grade?.name || "Grade non défini"}
                  </span>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Boutons d'administration */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {user?.roles?.includes("ROLE_ADMIN") && (
            <button
              onClick={handleSuperAdminAccess}
              className="group relative bg-gradient-to-r from-red-600 to-red-700 text-white p-4 rounded-xl shadow-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 flex items-center justify-center space-x-3"
            >
              <div className="flex flex-col items-center">
                <span className="text-lg font-semibold">Super Admin</span>
                <span className="text-xs opacity-75">Gestion globale</span>
              </div>
              <FaExternalLinkAlt className="h-4 w-4 group-hover:translate-x-1 transition-transform" />
            </button>
          )}

          {user?.roles?.includes("ROLE_INSTRUCTOR") && (
            <button
              onClick={handleAdminAccess}
              className="group relative bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-xl shadow-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 flex items-center justify-center space-x-3"
            >
              <div className="flex flex-col items-center">
                <span className="text-lg font-semibold">MyDojang</span>
                <span className="text-xs opacity-75">Gestion du dojang</span>
              </div>
              <FaExternalLinkAlt className="h-4 w-4 group-hover:translate-x-1 transition-transform" />
            </button>
          )}

          {!user?.roles?.includes("ROLE_INSTRUCTOR") &&
            !user?.roles?.includes("ROLE_ADMIN") && (
              <button
                onClick={() => setIsModalOpen(true)}
                className="w-full bg-gray-600 text-white py-2 px-4 rounded-lg shadow hover:bg-gray-700 transition-colors"
              >
                Voir informations complètes
              </button>
            )}
        </div>

        {userRole === "ROLE_INSTRUCTOR" && (
          <button
            onClick={handleContactAdmin}
            className="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          >
            Contacter l'administrateur
          </button>
        )}
      </div>

      {/* Modal pour les informations complètes */}
      {isModalOpen && <UserModal />}
    </div>
  );
}
