import React, { useState, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
    FaUserCircle,
    FaMapMarkerAlt,
    FaExternalLinkAlt,
} from "react-icons/fa";
import { fetchUserProfile } from "../../store/slices/profileSlice";
import dojoImage from "../../assets/icones/dojo.png";
import axios from "axios";

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";
console.log(API_URL);
console.log(process.env.REACT_APP_API_URL); // Dans docker-compose.yml ?


export default function Profil() {

    const dispatch = useDispatch();
    const { userData, isLoading, error } = useSelector((state) => state.profile);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedStudent, setSelectedStudent] = useState(null);
    const [dojangData, setDojangData] = useState(null);
    const [studentGrades, setStudentGrades] = useState({});
    const userRole = userData?.user?.roles?.[0];

    // Récupérer les données du dojang
    useEffect(() => {
        const fetchDojang = async () => {
            if (userData?.user?.dojang) {
                try {
                    const dojangId =
                        typeof userData.user.dojang === "string"
                            ? userData.user.dojang.split("/").pop()
                            : userData.user.dojang.id;

                    if (!dojangId) {
                        console.warn("Dojang ID is missing.");
                        return;
                    }

                    const response = await axios.get(`${API_URL}/api/dojangs/${dojangId}`);
                    setDojangData(response.data);
                } catch (error) {
                    console.error("Erreur lors du chargement du dojang:", error);
                    setDojangData({ name: "Non défini", city: "Non définie" });
                }
            }
        };

        fetchDojang();
    }, [userData]);

    // Récupérer les données des grades pour chaque étudiant
    useEffect(() => {
        const fetchStudentGrades = async () => {
            const grades = {};
            if (userData?.students) {
                for (const student of userData.students) {
                    if (student.grade) {
                        try {
                            const gradeId =
                                typeof student.grade === "string"
                                    ? student.grade.split("/").pop()
                                    : student.grade.id;

                            if (!gradeId) {
                                console.warn(`grade ID manquant pour student ${student.id}.`);
                                grades[student.id] = "Non défini";
                                continue;
                            }

                            const response = await axios.get(`${API_URL}/api/grades/${gradeId}`);
                            grades[student.id] = response.data.name;
                        } catch (error) {
                            console.error(
                                `Erreur lors du chargement du grade pour student ${student.id}:`,
                                error
                            );
                            grades[student.id] = "Non défini";
                        }
                    } else {
                        grades[student.id] = "Non défini";
                    }
                }
            }
            setStudentGrades(grades);
        };

        fetchStudentGrades();
    }, [userData?.students]);

    // Récupérer les données de l'utilisateur (y compris les informations des étudiants)
    useEffect(() => {
        dispatch(fetchUserProfile());
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
    const students = userData?.students || [];
    const studentuser = !selectedStudent ? setSelectedStudent(user) : "coco";

    // Modale informations complètes de l'étudiant
    const UserModal = () => {
        const displayUser = selectedStudent || studentuser;
        console.log("displayUser", displayUser);
        // console.log("API_URL", API_URL);
        // console.log("process.env.REACT_APP_API_URL", process.env.REACT_APP_API_URL);
        return (
            <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div className="bg-white p-6 rounded-lg max-w-2xl mx-auto">
                    <h2 className="text-2xl font-bold mb-4">Informations complètes</h2>
                    <div className="space-y-4">
                        <div>
                            <h3 className="font-semibold text-gray-700">
                                Informations personnelles
                            </h3>
                            <p>Nom complet: {displayUser?.firstname} {displayUser?.lastname}</p>
                            <p>Grade: {studentGrades[displayUser.id] || displayUser?.grade?.name || "Grade non défini"}</p>
                            <p>Date de naissance: {displayUser?.birthdate ? new Date(displayUser.birthdate).toLocaleDateString() : "Non définie"}</p>
                            <p>Email: {displayUser?.mail} </p>
                            <p>Adresse: {displayUser?.adress || "Non définie"}</p>
                            <p>Sexe: {displayUser?.sexe || "Non défini"}</p>
                            <p>Téléphone: {displayUser?.tel || "Non défini"}</p>
                            <p>Passport: {displayUser?.passport || "Non défini"}</p>
                        </div>
                        <div>
                            <h3 className="font-semibold text-gray-700">Dojang</h3>
                            <p>Nom: {dojangData?.name || "Non défini"}</p>
                            <p>Ville: {dojangData?.city || "Non définie"}</p>
        
                        </div>
                    </div>
                    <button
                        onClick={() => {
                            setIsModalOpen(false);
                            setSelectedStudent(null);
                        }}
                        className="mt-6 w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Fermer
                    </button>
                </div>
            </div>
        );
    };

    // Accéder à la page MyDojang
    const handleAdminAccess = () => {
        window.open(`${API_URL}/mydojang`, "_blank");
    };

    // Accéder à la page Admin
    const handleSuperAdminAccess = () => {
        window.open(`${API_URL}/admin`, "_blank");
    };

    // Contacter l'administrateur par email
    const handleContactAdmin = () => {
        window.location.href = "mailto:quentin.pecharroman13@gmail.com";
    };

    console.log("user", user);


    return (
        // Page profil
        <div className="bg-gray-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
            <div className="max-w-3xl mx-auto space-y-8">
                {/* User Profile */}
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
                            <span className="text-gray-600">Grade:</span>
                            <span className="font-medium text-gray-900">
                                {user?.grade?.name || "Non défini"}
                            </span>
                        </div>
                        <div className="flex items-center justify-between">
                            <span className="text-gray-600">Role:</span>
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

                {/* Dojang */}
                {dojangData && (
                    <div className="bg-white rounded-xl shadow-md p-6 flex items-center space-x-4">
                        <img
                            src={dojoImage}
                            alt="Dojo"
                            className="h-24 w-24 rounded-lg object-cover"
                        />
                        <div>
                            <h2 className="text-xl font-semibold text-gray-900">{dojangData.name}</h2>
                            <p className="text-sm text-gray-600 mt-1 flex items-center">
                                <FaMapMarkerAlt className="mr-2" />
                                {dojangData.city || "Adresse non disponible"}
                            </p>
                        </div>
                    </div>
                )}

                {/* Students */}
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
                                <button
                                    key={student.id}
                                    className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                    onClick={() => {
                                        setSelectedStudent(student);
                                        setIsModalOpen(true);
                                    }}
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
                                        {studentGrades[student.id] || "Grade non défini"}
                                    </span>
                                </button>
                            ))}
                        </div>
                    </div>
                )}

                {/* Boutons Gestion Admin */}
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

                {isModalOpen && <UserModal />}
            </div>
        </div>
    );
}
