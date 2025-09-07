<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation de réservation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Bonjour {{ $reservations[0]->prenom_client }} {{ $reservations[0]->nom_client }},</h2>

    <p>Merci pour votre réservation. Voici les détails :</p>

    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead style="background-color: #f2f2f2;">
            <tr>
                <th>Chambre</th>
                <th>Date d'arrivée</th>
                <th>Date de départ</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->chambre->nom ?? 'Chambre #' . $reservation->chambre_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($reservation->date_arrivee)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($reservation->date_depart)->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($reservation->statut) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <p>🛑 <strong>Code d'annulation :</strong></p>
    <h3 style="color: red;">{{ $reservations[0]->code_annulation }}</h3>
    <p>Conservez ce code précieusement. Il vous permettra d’annuler votre réservation sans compte.</p>

    <p>Vous pouvez annuler votre réservation à tout moment en cliquant ici :</p>
    <p>
        <a href="{{ url('/annuler-reservation?code=' . $reservations[0]->code_annulation) }}" style="color: #007bff;">
            Annuler ma réservation
        </a>
    </p>

    <br>

    <p>Nous restons à votre disposition pour toute question.</p>
    <p>À bientôt !</p>
</body>
</html>
