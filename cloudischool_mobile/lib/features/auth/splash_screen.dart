import 'package:flutter/material.dart';
import 'package:animate_do/animate_do.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/theme/app_colors.dart';
import '../../features/auth/auth_notifier.dart';
import '../../features/auth/login_screen.dart';
import '../../features/main_navigation.dart';

class SplashScreen extends ConsumerStatefulWidget {
  const SplashScreen({super.key});

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _startAnimation();
  }

  void _startAnimation() async {
    await Future.delayed(const Duration(seconds: 4));
    if (mounted) {
      final authState = ref.read(authNotifierProvider);
      
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => authState.isAuthenticated 
              ? const MainNavigationWrapper() 
              : const LoginScreen(),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        decoration: const BoxDecoration(
          gradient: AppColors.navyShine,
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            FadeInDown(
              child: const Icon(
                Icons.school_rounded,
                size: 100,
                color: AppColors.goldMetallic,
              ),
            ),
            const SizedBox(height: 20),
            FadeInUp(
              delay: const Duration(milliseconds: 500),
              child: Text(
                'CloudiSchool',
                style: GoogleFonts.playfairDisplay(
                  fontSize: 48,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                  letterSpacing: -1.5,
                ),
              ),
            ),
            const SizedBox(height: 10),
            FadeIn(
              delay: const Duration(milliseconds: 1000),
              child: const Text(
                'Empowering Education',
                style: TextStyle(
                  color: AppColors.goldShine,
                  fontSize: 18,
                  letterSpacing: 2,
                  fontWeight: FontWeight.w300,
                ),
              ),
            ),
            const SizedBox(height: 50),
            const CircularProgressIndicator(color: AppColors.goldMetallic),
          ],
        ),
      ),
    );
  }
}
