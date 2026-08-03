<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Nouvelle commande</title></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h1 style="color: #ea580c;">Nouvelle commande #{{ $commande->id }}</h1>
    <p>Bonjour {{ $gerant->nom }},</p>
    <p>Une nouvelle commande a été passée sur votre restaurant <strong>{{ $commande->restaurant->nom }}</strong>.</p>
    <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <p><strong>Client:</strong> {{ $commande->client->nom }}</p>
        <p><strong>Total:</strong> {{ number_format($commande->total, 2) }} €</p>
        <p><strong>Adresse:</strong> {{ $commande->adresse_livraison }}</p>
    </div>
    <a href="{{ route('gerant.commandes.show', $commande) }}" style="display: inline-block; background: #ea580c; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Voir la commande</a>
</body>
</html>
