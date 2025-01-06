import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchEvents, toggleEventParticipation } from "../store/slices/eventsSlice";
import { motion, AnimatePresence } from "framer-motion";
import { ChevronDownIcon } from "@heroicons/react/24/outline";
import { format } from "date-fns";
import { fr } from "date-fns/locale";

function FormationsList() {
  const dispatch = useDispatch();
  const { events, loading, error } = useSelector((state) => state.events);
  const { userData } = useSelector((state) => state.profile);
  const [expandedEvents, setExpandedEvents] = useState({});

  useEffect(() => {
    dispatch(fetchEvents());
  }, [dispatch]);

  const toggleEventExpansion = (eventId) => {
    setExpandedEvents(prev => ({
      ...prev,
      [eventId]: !prev[eventId]
    }));
  };

  const isUserParticipating = (formation) => {
    if (!userData?.user) return false;
    
    if (userData.user.roles.includes("ROLE_INSTRUCTOR")) {
      return formation.instructorParticipants?.some(p => p.id === userData.user.id);
    }
    return formation.studentParticipants?.some(p => p.id === userData.user.id);
  };

  const handleToggleParticipation = async (formationId) => {
    const isParticipating = isUserParticipating(events.find(e => e.id === formationId));
    await dispatch(toggleEventParticipation({ formationId, isParticipating }));
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center text-red-500 mt-4 p-4">
        {error}
      </div>
    );
  }

  return (
    <div className="bg-gray-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
      <div className="max-w-5xl mx-auto">
        <h1 className="text-3xl font-bold text-gray-900 mb-8">
          Événements à venir
        </h1>
        
        <div className="space-y-6">
          {events.map((formation) => (
            <div
              key={formation.id}
              id={`event-${formation.id}`}
              className="bg-white rounded-xl shadow-sm overflow-hidden"
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
      </div>
    </div>
  );
}

export default FormationsList;