<?php
/**
 * Template Name: Regolamento
 * Pagina regolamento con stile identico alla homepage
 */

get_header(); ?>

<body class="bg-gray-50">

    <!-- Header -->
    <header id="header" class="fixed top-0 w-full bg-white border-b border-gray-200 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <a href="<?php echo home_url(); ?>" class="w-10 h-10 instagram-gradient rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">IC</span>
            </a>
            <h1 class="text-black font-bold text-lg">Regolamento</h1>
            <div class="w-10 h-10"></div> <!-- Spacer -->
        </div>
    </header>

    <!-- Contenuto Regolamento -->
    <section class="mt-16 px-4 py-6 bg-white">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header Sezione -->
            <div class="text-center mb-8">
                <div class="text-4xl mb-4">📋</div>
                <h2 class="text-black font-bold text-2xl mb-4">Regolamento Contest</h2>
                <p class="text-gray-600 text-lg">Partecipa in sicurezza ai nostri concorsi Instagram</p>
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-blue-800 text-sm">
                        <span class="font-semibold">📅 Ultimo aggiornamento:</span> <?php echo date('d/m/Y'); ?>
                    </p>
                </div>
            </div>

            <!-- Contenuto del regolamento -->
            <div class="space-y-8">

                <!-- 1. Informazioni Generali -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">ℹ️</span>
                        <span>1. Informazioni Generali</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p><strong>InstaContest</strong> è una piattaforma che organizza concorsi a premi tramite Instagram. I contest sono gratuiti e aperti a tutti gli utenti maggiorenni residenti in Italia.</p>
                        
                        <p><strong>Organizzatore:</strong> InstaContest<br>
                        <strong>Sito web:</strong> www.instacontest.it<br>
                        <strong>Email:</strong> info@instacontest.it</p>
                        
                        <p>Partecipando ai nostri contest accetti integralmente questo regolamento e le condizioni d'uso del servizio.</p>
                    </div>
                </div>

                <!-- 2. Chi può partecipare -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">👥</span>
                        <span>2. Chi può partecipare</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p>Possono partecipare ai contest:</p>
                        <ul class="list-disc list-inside space-y-2 ml-4">
                            <li>Persone fisiche <strong>maggiorenni</strong> (18+ anni)</li>
                            <li>Residenti in <strong>Italia</strong></li>
                            <li>Possessori di un <strong>account Instagram pubblico</strong></li>
                            <li>Chi accetta integralmente questo regolamento</li>
                        </ul>
                        
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                            <h4 class="font-semibold text-red-800 mb-2">❌ Non possono partecipare:</h4>
                            <ul class="text-red-700 text-sm space-y-1">
                                <li>• Dipendenti di InstaContest e loro familiari</li>
                                <li>• Account Instagram privati o non verificabili</li>
                                <li>• Account fake, bot o duplicati</li>
                                <li>• Chi non rispetta le regole della community</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3. Come partecipare -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">🎯</span>
                        <span>3. Come partecipare</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p>Per partecipare a un contest devi:</p>
                        
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4">
                            <ol class="list-decimal list-inside space-y-3">
                                <li><strong>Seguire le istruzioni</strong> specifiche di ogni contest (like, commento, tag, ecc.)</li>
                                <li><strong>Avere un account Instagram pubblico</strong> durante tutto il contest</li>
                                <li><strong>Completare TUTTE le azioni richieste</strong> entro la scadenza</li>
                                <li><strong>Non cancellare</strong> like, commenti o follow durante il contest</li>
                            </ol>
                        </div>
                        
                        <p><strong>⏰ Tempistiche:</strong> La partecipazione è valida solo se completata entro la data e ora di scadenza indicate nel contest.</p>
                        
                        <p><strong>🔄 Modifiche:</strong> Non è possibile modificare la partecipazione dopo la scadenza del contest.</p>
                    </div>
                </div>

                <!-- 4. Selezione vincitori -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">🏆</span>
                        <span>4. Selezione dei vincitori</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p>I vincitori vengono selezionati tramite:</p>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-800 mb-2">🎲 Estrazione casuale</h4>
                                <p class="text-blue-700 text-sm">Utilizziamo sistemi di estrazione random per garantire equità e trasparenza.</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="font-semibold text-green-800 mb-2">✅ Verifica requisiti</h4>
                                <p class="text-green-700 text-sm">Controlliamo che tutte le condizioni di partecipazione siano rispettate.</p>
                            </div>
                        </div>
                        
                        <p><strong>📢 Comunicazione:</strong> Il vincitore viene annunciato sul nostro sito e profili social entro 7 giorni dalla chiusura del contest.</p>
                        
                        <p><strong>📞 Contatto:</strong> Il vincitore viene contattato tramite Instagram o email entro 14 giorni. Ha <strong>7 giorni</strong> per rispondere, altrimenti si procede con un nuovo sorteggio.</p>
                    </div>
                </div>

                <!-- 5. Premi e consegna -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">🎁</span>
                        <span>5. Premi e consegna</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p><strong>🎯 Natura dei premi:</strong> I premi variano per ogni contest e sono sempre specificati nella descrizione del concorso.</p>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-800 mb-2">📦 Modalità di consegna:</h4>
                            <ul class="text-yellow-700 text-sm space-y-1">
                                <li>• <strong>Prodotti fisici:</strong> Spedizione gratuita in Italia</li>
                                <li>• <strong>Buoni regalo:</strong> Invio digitale via email</li>
                                <li>• <strong>Servizi:</strong> Coordinamento diretto con il vincitore</li>
                            </ul>
                        </div>
                        
                        <p><strong>⏱️ Tempi di consegna:</strong> 15-30 giorni lavorativi dalla conferma dei dati del vincitore.</p>
                        
                        <p><strong>🚫 Limitazioni:</strong> I premi non sono convertibili in denaro, non sono trasferibili e non possono essere sostituiti salvo impossibilità dell'organizzatore.</p>
                    </div>
                </div>

                <!-- 6. Sistema punti -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">🏅</span>
                        <span>6. Sistema punti e classifica</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p>InstaContest utilizza un sistema di punteggi per premiare la partecipazione:</p>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h4 class="font-semibold text-purple-800 mb-2">🎯 Punti partecipazione</h4>
                                <p class="text-purple-700 text-sm">Ottieni punti per ogni contest a cui partecipi (generalmente 5 punti).</p>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-800 mb-2">🏆 Punti vittoria</h4>
                                <p class="text-yellow-700 text-sm">Punti bonus per ogni contest vinto (generalmente 50 punti).</p>
                            </div>
                        </div>
                        
                        <p><strong>📊 Classifica:</strong> I punti determinano la tua posizione nella classifica generale del sito.</p>
                        
                        <p><strong>🔐 Requisiti:</strong> Per accumulare punti devi essere registrato e loggato sulla piattaforma.</p>
                    </div>
                </div>

                <!-- 7. Comportamenti vietati -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">⚠️</span>
                        <span>7. Comportamenti vietati</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="font-semibold text-red-800 mb-3">❌ È severamente vietato:</h4>
                            <ul class="text-red-700 text-sm space-y-2">
                                <li>• Utilizzare account fake, bot o automatismi</li>
                                <li>• Creare account multipli per partecipare più volte</li>
                                <li>• Inserire dati falsi o non verificabili</li>
                                <li>• Molestare altri partecipanti o l'organizzazione</li>
                                <li>• Tentare di manipolare i risultati dei contest</li>
                                <li>• Violare le regole della community Instagram</li>
                                <li>• Pubblicare contenuti offensivi, illegali o inappropriati</li>
                            </ul>
                        </div>
                        
                        <p><strong>🚨 Conseguenze:</strong> Chi viola queste regole sarà <strong>squalificato</strong> e <strong>bannato</strong> da tutti i contest futuri.</p>
                    </div>
                </div>

                <!-- 8. Privacy e dati -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">🔒</span>
                        <span>8. Privacy e trattamento dati</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p>I tuoi dati personali sono protetti secondo il <strong>GDPR</strong> e la normativa italiana sulla privacy:</p>
                        
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-800 mb-2">📝 Dati raccolti:</h4>
                            <ul class="text-blue-700 text-sm space-y-1">
                                <li>• Username Instagram (pubblico)</li>
                                <li>• Email e dati di contatto (solo vincitori)</li>
                                <li>• Dati di spedizione (solo vincitori)</li>
                                <li>• Statistiche di partecipazione (anonime)</li>
                            </ul>
                        </div>
                        
                        <p><strong>🎯 Finalità:</strong> I dati vengono utilizzati esclusivamente per gestire i contest, contattare i vincitori e migliorare il servizio.</p>
                        
                        <p><strong>🗑️ Cancellazione:</strong> Puoi richiedere la cancellazione dei tuoi dati in qualsiasi momento scrivendo a privacy@instacontest.it</p>
                    </div>
                </div>

                <!-- 9. Limitazioni e responsabilità -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">⚖️</span>
                        <span>9. Limitazioni e responsabilità</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p>InstaContest si riserva il diritto di:</p>
                        
                        <ul class="list-disc list-inside space-y-2 ml-4">
                            <li>Modificare o annullare contest per motivi tecnici o legali</li>
                            <li>Squalificare partecipanti che violano il regolamento</li>
                            <li>Richiedere documenti di identità per verificare l'identità dei vincitori</li>
                            <li>Utilizzare nomi e foto dei vincitori a scopi promozionali (salvo rifiuto esplicito)</li>
                        </ul>
                        
                        <div class="bg-gray-50 rounded-lg p-4 mt-4">
                            <p class="text-gray-600 text-sm"><strong>📋 Nota importante:</strong> InstaContest non è responsabile per problemi tecnici di Instagram, interruzioni del servizio, o problemi di connessione che impediscano la partecipazione.</p>
                        </div>
                    </div>
                </div>

                <!-- 10. Modifiche e contatti -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-3">
                        <span class="text-2xl">📞</span>
                        <span>10. Modifiche e contatti</span>
                    </h3>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p><strong>📝 Modifiche al regolamento:</strong> Questo regolamento può essere aggiornato in qualsiasi momento. Le modifiche sono valide dalla data di pubblicazione sul sito.</p>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold text-green-800 mb-3">📧 Per qualsiasi domanda contattaci:</h4>
                            <ul class="text-green-700 text-sm space-y-1">
                                <li>• <strong>Email generale:</strong> info@instacontest.it</li>
                                <li>• <strong>Privacy:</strong> privacy@instacontest.it</li>
                                <li>• <strong>Contest:</strong> contest@instacontest.it</li>
                                <li>• <strong>Instagram:</strong> @instacontest</li>
                            </ul>
                        </div>
                        
                        <p><strong>⚖️ Foro competente:</strong> Per qualsiasi controversia è competente il Tribunale di Milano.</p>
                    </div>
                </div>

                <!-- Footer regolamento -->
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl p-6 text-white text-center">
                    <h3 class="font-bold text-xl mb-3">🎉 Buona fortuna!</h3>
                    <p class="text-white/90 mb-4">Partecipa ai nostri contest in sicurezza e divertiti!</p>
                    <a href="<?php echo home_url(); ?>" class="inline-flex items-center space-x-2 bg-white text-purple-600 font-bold py-3 px-6 rounded-xl hover:bg-gray-100 transition-colors">
                        <i class="fa-solid fa-trophy"></i>
                        <span>Scopri i contest aperti</span>
                    </a>
                </div>

            </div>
        </div>
    </section>

</body>

<?php get_footer(); ?>
