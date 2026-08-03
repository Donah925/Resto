<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Réservation confirmée</title></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h1 style="color: #ea580c;">Réservation confirmée</h1>
    <p>Bonjour {{ $reservation->client->prenom }},</p>
    <p>Votre réservation au restaurant <strong>{{ $reservation->restaurant->nom }}</strong> a été confirmée.</p>
    <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <p><strong>Date:</strong> {{ $reservation->date_reservation->format('d/m/Y à H:i') }}</p>
        <p><strong>Personnes:</strong> {{ $reservation->nombre_personnes }}</p>
        <p><strong>Table:</strong> {{ $reservation->table ?? 'À définir' }}</p>
    </div>
    <a href="{{ route('client.reservations.show', $reservation) }}" style="display: inline-block; background: #ea580c; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Voir ma réservation</a>
</body>
</html>
