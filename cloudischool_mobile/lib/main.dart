import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'app.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Future: Initialize plugins like Gemini API or Firebase here if needed
  
  runApp(
    const ProviderScope(
      child: CloudiSchoolApp(),
    ),
  );
}
