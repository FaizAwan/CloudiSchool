import 'package:flutter/material.dart';

class AppColors {
  // Primary Metallic Palette
  static const Color navyDeep = Color(0xFF0D1B2A);
  static const Color navyMiddle = Color(0xFF1B263B);
  static const Color navyBright = Color(0xFF415A77);
  
  static const Color goldMetallic = Color(0xFFFFD60A);
  static const Color goldShine = Color(0xFFFFC300);
  
  // Neutral Cinematic Palette
  static const Color surface = Color(0xFFF8F9FA);
  static const Color background = Color(0xFFFFFFFF);
  static const Color textMain = Color(0xFF0D1B2A);
  static const Color textMuted = Color(0xFF778DA9);
  
  // Gradients (The "Stitch" Look)
  static const LinearGradient navyShine = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [
      Color(0xFF3B82F6),
      Color(0xFF1E40AF),
      Color(0xFF172554),
    ],
  );

  static const LinearGradient metallicGold = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFFF3D078),
      Color(0xFFC9A041),
      Color(0xFF8A6E2A),
    ],
  );

  static const LinearGradient darkSpace = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFF0F172A),
      Color(0xFF020617),
    ],
  );
  
  // Glassmorphism Soft Border
  static final Color softBorder = const Color(0xFFFFFFFF).withOpacity(0.1);
}
