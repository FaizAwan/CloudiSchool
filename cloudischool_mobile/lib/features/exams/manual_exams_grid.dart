import 'package:animate_do/animate_do.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/theme/app_colors.dart';
import '../../core/widgets/glass_card.dart';

class ManualExamsScreen extends ConsumerStatefulWidget {
  const ManualExamsScreen({super.key});

  @override
  ConsumerState<ManualExamsScreen> createState() => _ManualExamsScreenState();
}

class _ManualExamsScreenState extends ConsumerState<ManualExamsScreen> {
  final _marksData = <int, String>{}; // studentId: marks

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        title: const Text('Manual Exam Entry'),
        backgroundColor: Colors.transparent,
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(20),
            child: GlassCard(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
              child: Column(
                children: [
                  DropdownButtonFormField<String>(
                    decoration: const InputDecoration(labelText: 'Select Class'),
                    items: ['Grade 1', 'Grade 2', 'Grade 3']
                        .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                        .toList(),
                    onChanged: (val) {},
                  ),
                  const SizedBox(height: 16),
                  DropdownButtonFormField<String>(
                    decoration: const InputDecoration(labelText: 'Select Subject'),
                    items: ['Mathematics', 'Science', 'English']
                        .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                        .toList(),
                    onChanged: (val) {},
                  ),
                ],
              ),
            ),
          ),
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              itemCount: 10, // Mock students
              itemBuilder: (context, index) {
                return FadeInLeft(
                  delay: Duration(milliseconds: index * 50),
                  child: Padding(
                    padding: const EdgeInsets.only(bottom: 12),
                    child: GlassCard(
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: AppColors.navyDeep.withOpacity(0.1),
                          child: Text((index + 1).toString()),
                        ),
                        title: Text('Student #${index + 1}'),
                        subtitle: const Text('Roll No: 10234'),
                        trailing: SizedBox(
                          width: 80,
                          child: TextField(
                            keyboardType: TextInputType.number,
                            textAlign: TextAlign.center,
                            decoration: InputDecoration(
                              hintText: '00',
                              fillColor: Colors.white,
                              contentPadding: const EdgeInsets.symmetric(vertical: 0),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: AppColors.background,
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
            ),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {},
                child: const Text('Save Marks'),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
