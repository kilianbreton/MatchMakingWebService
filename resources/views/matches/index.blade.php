<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchs en cours</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans">

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 text-center">Matchs en cours</h1>

        <div class="grid gap-6">
            @foreach($matches as $match)
           

                <div class="bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col md:flex-row justify-between items-center">
                    <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6 w-full">

                        <!-- Blue Team -->
                        <div class="flex flex-col text-blue-400 space-y-1">
                            <span class="font-bold">Bleue Team</span>
                            @foreach($match->playersA as $player)
                                <span>{{ $player->player->name ?? $player->player->login }}</span>
                            @endforeach
                        </div>

                        <!-- Score -->
                        <div class="text-center mx-auto">
                            <span class="text-3xl font-bold text-blue-400">{{ $teamBlue->sum('score') ?? 0 }}</span>
                            <span class="text-xl font-bold mx-2">-</span>
                            <span class="text-3xl font-bold text-red-500">{{ $teamRed->sum('score') ?? 0 }}</span>

                            <div class="text-green-400 font-semibold text-sm mt-1">IN PROGRESS</div>
                            <div class="text-gray-400 font-semibold text-sm">FINISHED</div>
                        </div>

                        <!-- Red Team -->
                        <div class="flex flex-col text-red-400 space-y-1">
                            <span class="font-bold">Red Team</span>
                            @foreach($match->playersBas $player)
                                <span>{{ $player->player->name ?? $player->player->login }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Server info -->
                    <div class="mt-4 md:mt-0 text-gray-400 text-sm">
                        Server: {{ $match->server->name ?? $match->server->login }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>