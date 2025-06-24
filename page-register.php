<?php
/**
 * Template Name: Registrazione
 * Pagina di registrazione completa
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main register-page min-h-screen bg-gradient-to-br from-purple-50 to-pink-50">
        
        <!-- Header con logo -->
        <div class="bg-white shadow-sm">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <a href="<?php echo home_url(); ?>" class="flex items-center space-x-2">
                        <span class="text-2xl">🎯</span>
                        <span class="text-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            InstaContest
                        </span>
                    </a>
                    
                    <!-- Link al login -->
                    <a href="/login" class="text-purple-600 hover:text-purple-700 font-medium">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        Accedi
                    </a>
                </div>
            </div>
        </div>

        <!-- Form di registrazione -->
        <?php get_template_part('user-templates/register-form'); ?>

        <!-- Sezione Google OAuth (per dopo) -->
        <div class="container max-w-md mx-auto px-4 pb-8">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gradient-to-br from-purple-50 to-pink-50 text-gray-500">oppure</span>
                </div>
            </div>
            
            <div class="mt-6">
                <!-- Placeholder per Google OAuth -->
                <button class="w-full bg-white border border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-50 transition duration-200 shadow-sm opacity-50 cursor-not-allowed" disabled>
                    <i class="fab fa-google mr-2 text-red-500"></i>
                    Registrati con Google (Coming Soon)
                </button>
            </div>
        </div>

    </main>
</div>

<?php
// Non mostrare il footer per un design più pulito
// get_footer(); 
?>
