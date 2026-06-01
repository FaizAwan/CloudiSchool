import 'package:google_generative_ai/google_generative_ai.dart';
import 'package:riverpod_annotation/riverpod_annotation.dart';

part 'gen_ai_notifier.g.dart';

@riverpod
class GenAINotifier extends _$GenAINotifier {
  GenerativeModel? _model;

  @override
  bool build() {
    // Initializing with a placeholder. Should be replaced with a real key from .env or secret storage.
    const apiKey = 'YOUR_GEMINI_API_KEY'; 
    _model = GenerativeModel(model: 'gemini-3-pro-preview', apiKey: apiKey);
    return false; // isLoading
  }

  Future<String?> generateMotivationalMessage(String schoolName) async {
    if (_model == null) return null;
    
    final prompt = 'Generate a short, powerful motivational message for students at $schoolName. '
        'Focus on "Empowering Education" and school pride. Use elegant language.';
    
    try {
      final content = [Content.text(prompt)];
      final response = await _model!.generateContent(content);
      return response.text;
    } catch (e) {
      return 'Stay focused, stay inspired. The future belongs to you.';
    }
  }
}
