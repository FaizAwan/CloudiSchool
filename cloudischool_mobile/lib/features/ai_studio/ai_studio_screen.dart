import 'package:animate_do/animate_do.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'gen_ai_notifier.dart';
import '../../core/theme/app_colors.dart';
import '../../core/widgets/glass_card.dart';

class AIStudioScreen extends ConsumerStatefulWidget {
  const AIStudioScreen({super.key});

  @override
  ConsumerState<AIStudioScreen> createState() => _AIStudioScreenState();
}

class _AIStudioScreenState extends ConsumerState<AIStudioScreen> {
  String? _motivation;
  bool _isLoading = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        title: const Text('AI Creative Studio'),
        backgroundColor: Colors.transparent,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            FadeInDown(
              child: const GlassCard(
                padding: EdgeInsets.all(24),
                child: Column(
                  children: [
                    Icon(Icons.auto_awesome, color: AppColors.goldMetallic, size: 48),
                    SizedBox(height: 12),
                    Text(
                      'Gemini AI Assistant',
                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                    ),
                    Text(
                      'AI-powered motivational posters, announcements, and reports',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Colors.grey, fontSize: 13),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),
            FadeInUp(
              delay: const Duration(milliseconds: 200),
              child: GlassCard(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Create Motivational Poster',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SizedBox(height: 12),
                    if (_motivation != null)
                      Container(
                        padding: const EdgeInsets.all(16),
                        margin: const EdgeInsets.only(bottom: 20),
                        decoration: BoxDecoration(
                          color: AppColors.goldMetallic.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: AppColors.goldMetallic.withOpacity(0.3)),
                        ),
                        child: Text(
                          _motivation!,
                          style: const TextStyle(
                            fontStyle: FontStyle.italic,
                            fontSize: 15,
                            fontFamily: 'PlayfairDisplay',
                          ),
                        ),
                      ),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        icon: const Icon(Icons.psychology_outlined),
                        label: Text(_isLoading ? 'Generating...' : 'Generate Inspiration'),
                        onPressed: _isLoading
                            ? null
                            : () async {
                                setState(() => _isLoading = true);
                                final res = await ref.read(genAINotifierProvider.notifier).generateMotivationalMessage('CloudiSchool');
                                setState(() {
                                  _motivation = res;
                                  _isLoading = false;
                                });
                              },
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),
            FadeInUp(
              delay: const Duration(milliseconds: 400),
              child: GlassCard(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Veo Cinematic School Trailer',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      height: 180,
                      width: double.infinity,
                      decoration: BoxDecoration(
                        color: AppColors.navyDeep.withOpacity(0.05),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: AppColors.navyDeep.withOpacity(0.1)),
                      ),
                      child: const Center(
                        child: Icon(Icons.video_camera_back_outlined, size: 48, color: Colors.grey),
                      ),
                    ),
                    const SizedBox(height: 16),
                    const TextField(
                      decoration: InputDecoration(
                        hintText: 'Prompt (e.g., Happy students studying in futuristic school)',
                        fillColor: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 16),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        icon: const Icon(Icons.movie_creation_outlined),
                        label: const Text('Generate with Veo 3'),
                        onPressed: () {}, // Trigger Veo API from quickstart integration
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
