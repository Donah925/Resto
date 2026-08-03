<x-gerant-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Mon Restaurant
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Gérez les informations de votre établissement
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Annuler
                    </button>
                    <button type="button" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Enregistrer
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form class="space-y-8 divide-y divide-gray-200">
                <div class="space-y-8 divide-y divide-gray-200">
                    <!-- Informations générales -->
                    <div class="pt-8">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Informations générales</h3>
                        </div>
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="nom" class="block text-sm font-medium text-gray-700">Nom du restaurant</label>
                                <div class="mt-1">
                                    <input type="text" name="nom" id="nom" value="{{ $restaurant->nom ?? '' }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md p-3 border">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <div class="mt-1">
                                    <textarea id="description" name="description" rows="4" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md p-3 border">{{ $restaurant->description ?? '' }}</textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Décrivez votre restaurant en quelques mots.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <div class="mt-1">
                                    <input type="tel" name="telephone" id="telephone" value="{{ $restaurant->telephone ?? '' }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md p-3 border">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <div class="mt-1">
                                    <input type="email" name="email" id="email" value="{{ $restaurant->email ?? '' }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md p-3 border">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label for="logo" class="block text-sm font-medium text-gray-700">Logo</label>
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 cursor-pointer">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none">
                                                <span>Télécharger un fichier</span>
                                                <input id="file-upload" name="file-upload" type="file" class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 5MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="pt-8">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Adresse</h3>
                        </div>
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse complète</label>
                                <div class="mt-1">
                                    <input type="text" name="adresse" id="adresse" value="{{ $restaurant->adresse ?? '' }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md p-3 border">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="ville" class="block text-sm font-medium text-gray-700">Ville</label>
                                <div class="mt-1">
                                    <input type="text" name="ville" id="ville" value="{{ $restaurant->ville ?? '' }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md p-3 border">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="code_postal" class="block text-sm font-medium text-gray-700">Code postal</label>
                                <div class="mt-1">
                                    <input type="text" name="code_postal" id="code_postal" value="{{ $restaurant->code_postal ?? '' }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md p-3 border">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Localisation sur la carte</label>
                                <div class="mt-1 h-64 bg-gray-200 rounded-md flex items-center justify-center">
                                    <span class="text-gray-500">Carte interactive (Leaflet)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Horaires -->
                    <div class="pt-8">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Horaires d'ouverture</h3>
                        </div>
                        <div class="mt-6 space-y-4">
                            @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $jour)
                            <div class="flex items-center space-x-4">
                                <span class="w-24 text-sm font-medium text-gray-700">{{ $jour }}</span>
                                <div class="flex items-center space-x-2">
                                    <input type="time" name="horaires[{{ $jour }}][ouverture]" class="shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm border-gray-300 rounded-md p-2 border" value="11:00">
                                    <span class="text-gray-500">-</span>
                                    <input type="time" name="horaires[{{ $jour }}][fermeture]" class="shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm border-gray-300 rounded-md p-2 border" value="22:00">
                                </div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="horaires[{{ $jour }}][ouvert]" checked class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-600">Ouvert</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Zone de livraison -->
                    <div class="pt-8">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Zone de livraison</h3>
                            <p class="mt-1 text-sm text-gray-500">Définissez le rayon de livraison en kilomètres</p>
                        </div>
                        <div class="mt-6">
                            <label for="rayon_livraison" class="block text-sm font-medium text-gray-700">Rayon (km)</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="number" name="rayon_livraison" id="rayon_livraison" value="{{ $restaurant->rayon_livraison ?? 5 }}" min="1" max="50" class="focus:ring-red-500 focus:border-red-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 p-3 border">
                                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    km
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex justify-end">
                        <button type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Annuler
                        </button>
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-gerant-layout>
