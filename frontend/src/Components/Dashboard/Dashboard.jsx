import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchUserProfile } from "../../store/slices/profileSlice";
import { fetchEvents } from "../../store/slices/eventsSlice";
import { fetchOrders } from "../../store/slices/ordersSlice";
import { Link } from "react-router-dom";
import { FaUser, FaCalendarAlt, FaShoppingBag, FaBoxOpen, FaBox } from "react-icons/fa";
import { GiBlackBelt } from "react-icons/gi";
import { toast } from "react-toastify";

export default function Dashboard() {
  const dispatch = useDispatch();
  const {
    userData,
    isLoading: profileLoading,
    error: profileError,
  } = useSelector((state) => state.profile);
  const { events, loading: eventsLoading } = useSelector(
    (state) => state.events
  );
  const {
    orders,
    loading: ordersLoading,
    error: ordersError,
  } = useSelector((state) => state.orders);

  useEffect(() => {
    dispatch(fetchUserProfile());
    dispatch(fetchOrders());
    dispatch(fetchEvents());
  }, [dispatch]);

  // Afficher uniquement les évenements qui ne sont pas passés auquel l'utilisateur est inscrit
  const getUpcomingEvents = () => {
    if (!events || !userData?.user) return [];

    const userEvents = events.filter((event) => {
      if (userData.user.roles.includes("ROLE_INSTRUCTOR")) {
        return (
          event.instructorParticipants &&
          Array.isArray(event.instructorParticipants) &&
          event.instructorParticipants.some((p) => p.id === userData.user.id)
        );
      }
      return (
        event.studentParticipants &&
        Array.isArray(event.studentParticipants) &&
        event.studentParticipants.some((p) => p.id === userData.user.id)
      );
    });

    const now = new Date();
    return userEvents
      .filter((event) => new Date(event.endDate) >= now)
      .sort((a, b) => new Date(a.startDate) - new Date(b.startDate)); // Trier par date
  };

  // Afficher tout les prochains évenements auquel l'utilisateur est inscrit
  // const getUserEvents = () => {
  //   if (!events || !userData?.user) return [];

  //   // console.log("Events:", events); // Pour le debug
  //   // console.log("User:", userData.user); // Pour le debug

  //   const userEvents = events.filter((event) => {
  //     if (userData.user.roles.includes("ROLE_INSTRUCTOR")) {
  //       return (
  //         event.instructorParticipants &&
  //         Array.isArray(event.instructorParticipants) &&
  //         event.instructorParticipants.some((p) => p.id === userData.user.id)
  //       );
  //     }
  //     return (
  //       event.studentParticipants &&
  //       Array.isArray(event.studentParticipants) &&
  //       event.studentParticipants.some((p) => p.id === userData.user.id)
  //     );
  //   });

  //   // console.log("Filtered events:", userEvents); // Pour le debug

  //   return userEvents.sort(
  //     (a, b) => new Date(a.startDate) - new Date(b.startDate)
  //   );
  // };
  // console.log(getUserEvents());

  // Afficher le statut de la commande
  const getStatusLabel = (status) => {
    const statusMap = {
      not_ordered: "Pas encore passée",
      pending: "En attente",
      received: "Arrivée",
    };
    return statusMap[status] || status;
  };

  // Afficher le chargement de la page
  if (profileLoading || ordersLoading) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  // Afficher le message d'erreur
  if (profileError || ordersError) {
    return (
      <div className="text-center text-red-500 mt-4">
        {profileError?.message || ordersError?.message || "API Error"}
      </div>
    );
  }

  // Afficher la prochaine commande
  const getNextOrder = () => {
    if (!orders || orders.length === 0) return null;
    const now = new Date();
    return orders
      .filter((order) => new Date(order.sendDate) >= now)
      .sort((a, b) => new Date(a.sendDate) - new Date(b.sendDate))[0]; // Commande la plus proche
  };
  // Afficher les 3 commandes précédentes
  const getPreviousOrders = () => {
    if (!orders || orders.length === 0) return [];
    const now = new Date();
    return orders
      .filter((order) => new Date(order.sendDate) < now)
      .sort((a, b) => new Date(b.sendDate) - new Date(a.sendDate)) // Trier par date décroissante
      .slice(0, 3); // Limiter à trois commandes
  };

  const nextOrder = getNextOrder();
  const previousOrders = getPreviousOrders();

  return (
    <div className="bg-gray-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
      <div className="max-w-5xl mx-auto space-y-8">
        {/* En-tête du profil */}
        <div className="bg-white rounded-xl shadow-sm p-6">
          <div className="flex items-center space-x-4">
            <div className="bg-blue-100 p-3 rounded-full">
              <FaUser className="h-6 w-6 text-blue-600" />
            </div>
            <div>
              <h1 className="text-2xl font-bold text-gray-900">
                {userData?.user?.firstname} {userData?.user?.lastname}
              </h1>
              <div className="flex space-x-4 mt-2">
                <span className="flex items-center text-gray-600">
                  <GiBlackBelt className="mr-2" />
                  {userData?.user?.grade?.name || "Grade non défini"}
                </span>
                <span className="text-gray-600">
                  Dojang: {userData?.user?.dojang?.name || "Non assigné"}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Section Événements */}
        <div className="bg-white rounded-xl shadow-sm p-6">
          <div className="flex items-center mb-6">
            <FaCalendarAlt className="h-5 w-5 text-blue-600 mr-2" />
            <h2 className="text-xl font-semibold text-gray-900">
              Événements à venir
            </h2>
          </div>
          <div className="space-y-4">
            {getUpcomingEvents().length > 0 ? (
              getUpcomingEvents().map((event) => (
                <Link
                  key={event.id}
                  to={`/events#event-${event.id}`}
                  className="block border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
                >
                  <h3 className="font-medium text-gray-900">{event.type}</h3>
                  <div className="mt-2 text-sm text-gray-600 space-y-1">
                    <p>
                      Date:{" "}
                      {new Date(event.startDate).toLocaleDateString("fr-FR")} -{" "}
                      {new Date(event.endDate).toLocaleDateString("fr-FR")}
                    </p>
                    <p>Lieu: {event.location}</p>
                  </div>
                </Link>
              ))
            ) : (
              <p className="text-gray-600">Aucun événement planifié</p>
            )}
          </div>
        </div>

        {/* Section Commandes */}
     {/* Section Commandes */}
<div className="bg-white rounded-xl shadow-sm p-6">
  {/* Prochaine commande */}
  <div className="flex items-center mb-6">
    <FaBox className="h-5 w-5 text-blue-600 mr-2" />
    <h2 className="text-xl font-semibold text-gray-900">
      Prochaine commande
    </h2>
  </div>
  {nextOrder ? (
    <div className="border border-gray-200 rounded-lg p-4">
      <div className="text-sm text-gray-600 space-y-2">
        <p>
          Date d'envoi :{" "}
          {new Date(nextOrder.sendDate).toLocaleDateString("fr-FR")}
        </p>
        <p>
          Statut :
          <span
            className={`ml-2 px-2 py-1 rounded-full text-xs ${
              nextOrder.status === "pending"
                ? "bg-yellow-100 text-yellow-800"
                : nextOrder.status === "received"
                ? "bg-green-100 text-green-800"
                : "bg-gray-100 text-gray-800"
            }`}
          >
            {getStatusLabel(nextOrder.status)}
          </span>
        </p>
      </div>
    </div>
  ) : (
    <p className="text-gray-600">Aucune commande en cours</p>
  )}

  {/* Commandes précédentes (3 max)*/}
  <div className="flex items-center mt-8 mb-6">
    <FaBoxOpen className="h-5 w-5 text-blue-600 mr-2" />
    <h2 className="text-xl font-semibold text-gray-900">
      Commandes précédentes
    </h2>
  </div>
  <div className="space-y-4">
    {previousOrders.length > 0 ? (
      previousOrders.map((order) => (
        <div
          key={order.id}
          className="border border-gray-200 rounded-lg p-4"
        >
          <div className="text-sm text-gray-600 space-y-2">
            <p>
              Date d'envoi :{" "}
              {new Date(order.sendDate).toLocaleDateString("fr-FR")}
            </p>
            <p>
              Statut :
              <span
                className={`ml-2 px-2 py-1 rounded-full text-xs ${
                  order.status === "pending"
                    ? "bg-yellow-100 text-yellow-800"
                    : order.status === "received"
                    ? "bg-green-100 text-green-800"
                    : "bg-gray-100 text-gray-800"
                }`}
              >
                {getStatusLabel(order.status)}
              </span>
            </p>
          </div>
        </div>
      ))
    ) : (
      <p className="text-gray-600">Aucune commande précédente</p>
    )}
  </div>
</div>

      </div>
    </div>
  );
}
