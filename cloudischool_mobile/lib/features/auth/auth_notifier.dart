import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/api/dio_client.dart';
import '../../core/constants/app_constants.dart';
import '../../shared/global_providers.dart';

class AuthState {
  final bool isLoading;
  final bool isAuthenticated;
  final String? error;
  final dynamic user;

  AuthState({
    this.isLoading = false,
    this.isAuthenticated = false,
    this.error,
    this.user,
  });

  AuthState copyWith({
    bool? isLoading,
    bool? isAuthenticated,
    String? error,
    dynamic user,
  }) {
    return AuthState(
      isLoading: isLoading ?? this.isLoading,
      isAuthenticated: isAuthenticated ?? this.isAuthenticated,
      error: error,
      user: user ?? this.user,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final DioClient _client;
  final Ref _ref;

  AuthNotifier(this._client, this._ref) : super(AuthState());

  Future<void> login(String email, String password) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final response = await _client.dio.post('/login', data: {
        'email': email,
        'password': password,
      });

      if (response.data['status'] == 'success') {
        final token = response.data['token'];
        await _ref.read(secureStorageProvider).write(
          key: AppConstants.tokenKey,
          value: token,
        );
        state = state.copyWith(
          isLoading: false,
          isAuthenticated: true,
          user: response.data['user'],
        );
      } else {
        state = state.copyWith(
          isLoading: false,
          error: response.data['message'] ?? 'Login failed',
        );
      }
    } on DioException catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.response?.data['message'] ?? 'Network error',
      );
    }
  }

  void logout() async {
    await _ref.read(secureStorageProvider).delete(key: AppConstants.tokenKey);
    state = AuthState();
  }
}

final authNotifierProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier(ref.watch(dioClientProvider), ref);
});
