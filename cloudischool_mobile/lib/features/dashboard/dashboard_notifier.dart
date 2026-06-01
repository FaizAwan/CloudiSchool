import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../shared/global_providers.dart';

class DashboardStats {
  final int studentsCount;
  final int teachersCount;
  final int parentsCount;
  final double monthlyRevenue;

  DashboardStats({
    required this.studentsCount,
    required this.teachersCount,
    required this.parentsCount,
    required this.monthlyRevenue,
  });

  factory DashboardStats.fromJson(Map<String, dynamic> json) {
    return DashboardStats(
      studentsCount: json['students_count'] ?? 0,
      teachersCount: json['teachers_count'] ?? 0,
      parentsCount: json['parents_count'] ?? 0,
      monthlyRevenue: (json['monthly_revenue'] ?? 0).toDouble(),
    );
  }
}

final dashboardStatsProvider = FutureProvider<DashboardStats>((ref) async {
  final client = ref.watch(dioClientProvider);
  final response = await client.dio.get('/dashboard/stats');
  return DashboardStats.fromJson(response.data['data']);
});
