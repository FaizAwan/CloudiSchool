import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../shared/global_providers.dart';
import 'student_model.dart';

class StudentState {
  final List<Student> students;
  final bool isLoading;
  final String? error;

  StudentState({this.students = const [], this.isLoading = false, this.error});

  StudentState copyWith({List<Student>? students, bool? isLoading, String? error}) {
    return StudentState(
      students: students ?? this.students,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

class StudentNotifier extends StateNotifier<StudentState> {
  final Ref _ref;
  StudentNotifier(this._ref) : super(StudentState()) {
    fetchStudents();
  }

  Future<void> fetchStudents() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final client = _ref.read(dioClientProvider);
      final response = await client.dio.get('/students');
      final list = (response.data['data'] as List).map((e) => Student.fromJson(e)).toList();
      state = state.copyWith(isLoading: false, students: list);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<bool> addStudent(Student student) async {
    state = state.copyWith(isLoading: true);
    try {
      final client = _ref.read(dioClientProvider);
      await client.dio.post('/students', data: student.toJson());
      await fetchStudents();
      return true;
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
      return false;
    }
  }

  Future<void> deleteStudent(int id) async {
    try {
      final client = _ref.read(dioClientProvider);
      await client.dio.delete('/students/$id');
      await fetchStudents();
    } catch (e) {
      state = state.copyWith(error: e.toString());
    }
  }
}

final studentProvider = StateNotifierProvider<StudentNotifier, StudentState>((ref) {
  return StudentNotifier(ref);
});
