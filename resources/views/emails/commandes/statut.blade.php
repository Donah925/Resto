<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Statut commande</title></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h1 style="color: #ea580c;">Statut de votre commande #{{ $commande->id }}</h1>
    <p>Bonjour {{ $client->prenom }},</p>
    <p>Le statut de votre commande a été mis à jour : <strong>{{ ucfirst($statut) }}</strong></p>
    <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <p><strong>Restaurant:</strong> {{ $commande->restaurant->nom }}</p>
        <p><strong>Total:</strong> {{ number_format($commande->total, 2) }} €</p>
    </div>
    <a href="{{ route('client.commandes.show', $commande) }}" style="display: inline-block; background: #ea580c; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Suivre ma commande</a>
</body>
</html>
