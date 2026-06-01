import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:riverpod_annotation/riverpod_annotation.dart';
import '../core/api/dio_client.dart';

part 'global_providers.g.dart';

@Riverpod(keepAlive: true)
FlutterSecureStorage secureStorage(SecureStorageRef ref) {
  return const FlutterSecureStorage();
}

@Riverpod(keepAlive: true)
Dio dio(DioRef ref) {
  return Dio();
}

@Riverpod(keepAlive: true)
DioClient dioClient(DioClientRef ref) {
  return DioClient(
    dio: ref.watch(dioProvider),
    secureStorage: ref.watch(secureStorageProvider),
  );
}

@riverpod
class ThemeModeNotifier extends _$ThemeModeNotifier {
  @override
  bool build() => false; // false = light, true = dark

  void toggle() => state = !state;
}
