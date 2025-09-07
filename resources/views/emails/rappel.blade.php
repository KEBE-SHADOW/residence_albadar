<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rappel de réservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bonjour {{ $reservation->prenom_client }} {{ $reservation->nom_client }},</h2>

        <p>Petit rappel : votre arrivée est prévue demain ({{ $reservation->date_arrivee }}) pour la chambre <strong>{{ $reservation->chambre->titre }}</strong>.</p>

        <p>Nous avons hâte de vous accueillir. N’hésitez pas à nous contacter si vous avez des questions ou des besoins particuliers.</p>

        <p class="footer">
            &copy; {{ date('Y') }} ALBADAR – Tous droits réservés.
        </p>
    </div>
</body>
</html>
