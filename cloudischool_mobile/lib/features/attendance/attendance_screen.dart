import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/widgets/glass_card.dart';

class AttendanceScreen extends StatefulWidget {
  const AttendanceScreen({super.key});

  @override
  State<AttendanceScreen> createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  DateTime _selectedDate = DateTime.now();
  final _attendanceStatus = <int, bool>{}; // studentId: isPresent

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        title: const Text('Attendance'),
        backgroundColor: Colors.transparent,
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(20),
            child: GlassCard(
              padding: const EdgeInsets.all(20),
              child: Row(
                children: [
                  const Icon(Icons.calendar_month_outlined, color: AppColors.navyDeep),
                  const SizedBox(width: 12),
                  Text(
                    '${_selectedDate.day}/${_selectedDate.month}/${_selectedDate.year}',
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
                  ),
                  const Spacer(),
                  TextButton(
                    onPressed: () async {
                      final picked = await showDatePicker(
                        context: context,
                        initialDate: _selectedDate,
                        firstDate: DateTime(2020),
                        lastDate: DateTime(2100),
                      );
                      if (picked != null) setState(() => _selectedDate = picked);
                    },
                    child: const Text('Change Date'),
                  ),
                ],
              ),
            ),
          ),
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              itemCount: 15, // Mock students
              itemBuilder: (context, index) {
                final studentId = index + 1;
                final isPresent = _attendanceStatus[studentId] ?? true;
                return Padding(
                  padding: const EdgeInsets.only(bottom: 12),
                  child: GlassCard(
                    child: ListTile(
                      title: Text('Student #$studentId'),
                      subtitle: const Text('ID: CS-293'),
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          _AttendanceToggle(
                            isPresent: true,
                            active: isPresent,
                            onTap: () => setState(() => _attendanceStatus[studentId] = true),
                          ),
                          const SizedBox(width: 8),
                          _AttendanceToggle(
                            isPresent: false,
                            active: !isPresent,
                            onTap: () => setState(() => _attendanceStatus[studentId] = false),
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
          Container(
            padding: const EdgeInsets.all(20),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {},
                child: const Text('Submit Attendance'),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _AttendanceToggle extends StatelessWidget {
  final bool isPresent;
  final bool active;
  final VoidCallback onTap;

  const _AttendanceToggle({
    required this.isPresent,
    required this.active,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final color = isPresent ? Colors.green : Colors.red;
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: active ? color.withOpacity(0.15) : Colors.grey.withOpacity(0.05),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: active ? color.withOpacity(0.5) : Colors.grey.withOpacity(0.2),
            width: 1,
          ),
        ),
        child: Text(
          isPresent ? 'Present' : 'Absent',
          style: TextStyle(
            color: active ? color : Colors.grey,
            fontWeight: active ? FontWeight.bold : FontWeight.normal,
            fontSize: 12,
          ),
        ),
      ),
    );
  }
}
