import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchEvents, toggleEventParticipation } from "../store/slices/eventsSlice";
import { motion, AnimatePresence } from "framer-motion";
import { ChevronDownIcon } from "@heroicons/react/24/outline";
import { format, isBefore, isAfter } from "date-fns";
import { fr } from "date-fns/locale";

function FormationsList() {
  const dispatch = useDispatch();
  const { events, loading, error } = useSelector((state) => state.events);
  const { userData } = useSelector((state) => state.profile);
  const [expandedEvents, setExpandedEvents] = useState({});
  const [showPastEvents, setShowPastEvents] = useState(false);

  // Récupérer tous les événements
  useEffect(() => {
    dispatch(fetchEvents());
  }, [dispatch]);

  // Afficher les événements
  const toggleEventExpansion = (eventId) => {
    setExpandedEvents(prev => ({
      ...prev,
      [eventId]: !prev[eventId]
    }));
  };

  // Vérifier si l'utilisateur participe à un événement
  const isUserParticipating = (formation) => {
    if (!userData?.user) return false;
    // Vérifier si l'utilisateur est un instructeur
    if (userData.user.roles.includes("ROLE_INSTRUCTOR")) {
      return formation.instructorParticipants?.some(participant => participant.id === userData.user.id);
    }
    // Vérifier si l'utilisateur est un étudiant
    if (userData.user.roles.includes("ROLE_STUDENT")) {
      return formation.studentParticipants?.some(participant => participant.id === userData.user.id);
    }
    return false;
  };

  // Gérer le changement de participation à un événement
  const handleToggleParticipation = async (formationId) => {
    const isParticipating = isUserParticipating(events.find(e => e.id === formationId));
    await dispatch(toggleEventParticipation({ formationId, isParticipating }));
  };

  // Filtrer les événements à venir
  const upcomingEvents = events.filter(event => isAfter(new Date(event.startDate), new Date()));
  // Filtrer les événements passés
  const pastEvents = events.filter(event => isBefore(new Date(event.endDate), new Date()));

  // Afficher la liste des événements à venir ou passés en fonction du paramètre isPast   
  const renderEventList = (eventList, isPast = false) => (
    <div className="space-y-6">
      {eventList.map((formation) => (
        <div
          key={formation.id}
          id={`event-${formation.id}`}
          className={`bg-white rounded-xl shadow-sm overflow-hidden ${isPast ? 'opacity-75' : ''}`}
        >
          <div className="p-6">
            <div className="flex justify-between items-start">
              <div>
                <h2 className="text-xl font-semibold text-gray-900">
                  {formation.type}
                </h2>
                <div className="mt-2 space-y-2">
                  <p className="text-sm text-gray-600">
                    <span className="font-medium">Date : </span>
                    {format(new Date(formation.startDate), "d MMMM yyyy", { locale: fr })}
                    {" - "}
                    {format(new Date(formation.endDate), "d MMMM yyyy", { locale: fr })}
                  </p>
                  <p className="text-sm text-gray-600">
                    <span className="font-medium">Lieu : </span>
                    {formation.location}
                  </p>
                  <p className="text-sm text-gray-600">
                    <span className="font-medium">Participants : </span>
                    {formation.participantsCount || 0}
                  </p>
                </div>
              </div>

              {/* Afficher le bouton d'inscription ou de désinscription en fonction de si  l'événement est passé ou non */}
              {!isPast && (
                <div className="flex space-x-4">
                  <button
                    onClick={() => handleToggleParticipation(formation.id)}
                    className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors
                      ${isUserParticipating(formation)
                        ? "bg-red-100 text-red-700 hover:bg-red-200"
                        : "bg-blue-100 text-blue-700 hover:bg-blue-200"
                      }`}
                  >
                    {isUserParticipating(formation) ? "Se désinscrire" : "S'inscrire"}
                  </button>
                </div>
              )}
            </div>

            <button
              onClick={() => toggleEventExpansion(formation.id)}
              className="mt-4 flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors"
            >
              <motion.span
                animate={{ rotate: expandedEvents[formation.id] ? 180 : 0 }}
                transition={{ duration: 0.3 }}
              >
                <ChevronDownIcon className="h-5 w-5" />
              </motion.span>
              <span className="ml-2">Voir les participants</span>
            </button>

            {/* Afficher les participants d'un événement */}  
            <AnimatePresence>
              {expandedEvents[formation.id] && (
                <motion.div
                  initial={{ height: 0, opacity: 0 }}
                  animate={{ height: "auto", opacity: 1 }}
                  exit={{ height: 0, opacity: 0 }}
                  transition={{ duration: 0.3 }}
                  className="overflow-hidden"
                >
                  <div className="mt-4 space-y-4">
                    {formation.instructorParticipants?.length > 0 && (
                      <div>
                        <h3 className="font-medium text-gray-900">Instructeurs</h3>
                        <div className="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                          {formation.instructorParticipants.map((instructor) => (
                            <div
                              key={instructor.id}
                              className="flex items-center space-x-2 text-sm text-gray-600 bg-gray-50 rounded-lg p-2"
                            >
                              <span>{instructor.firstname} {instructor.lastname}</span>
                            </div>
                          ))}
                        </div>
                      </div>
                    )}

                    {formation.studentParticipants?.length > 0 && (
                      <div>
                        <h3 className="font-medium text-gray-900">Étudiants</h3>
                        <div className="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                          {formation.studentParticipants.map((student) => (
                            <div
                              key={student.id}
                              className="flex items-center space-x-2 text-sm text-gray-600 bg-gray-50 rounded-lg p-2"
                            >
                              <span>{student.firstname} {student.lastname}</span>
                            </div>
                          ))}
                        </div>
                      </div>
                    )}
                  </div>
                </motion.div>
              )}
            </AnimatePresence>
          </div>
        </div>
      ))}
    </div>
  );

  // Afficher le chargement de la page
  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  // Afficher le message d'erreur
  if (error) {
    return (
      <div className="text-center text-red-500 mt-4 p-4">
        {error}
      </div>
    );
  }

  // Afficher la liste des événements
  return (
    <div className="bg-gray-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
      <div className="max-w-5xl mx-auto">
        <h1 className="text-3xl font-bold text-gray-900 mb-8">
          Événements à venir
        </h1>
        {/* Afficher la liste des événements à venir */}  
        {renderEventList(upcomingEvents)}

        <div className="mt-12">
          <button
            onClick={() => setShowPastEvents(!showPastEvents)}
            className="text-blue-600 hover:text-blue-800 font-medium"
          >
            {/* Afficher le bouton pour masquer ou afficher les événements passés */}
            {showPastEvents ? "Masquer les événements passés" : "Voir les événements passés"}
          </button>

          {showPastEvents && (
            <>
              <h2 className="text-2xl font-bold text-gray-900 mt-6 mb-4">
                Événements passés
              </h2>
              {/* Afficher la liste des événements passés */}
              {renderEventList(pastEvents, true)}
            </>
          )}
        </div>
      </div>
    </div>
  );
}

export default FormationsList;
