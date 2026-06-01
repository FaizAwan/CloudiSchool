import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../auth/auth_notifier.dart';
import '../../core/theme/app_colors.dart';
import '../../core/widgets/glass_card.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authNotifierProvider);

    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        title: const Text('Profile'),
        backgroundColor: Colors.transparent,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            const Center(
              child: Stack(
                children: [
                  CircleAvatar(
                    radius: 70,
                    backgroundColor: AppColors.navyDeep,
                    child: CircleAvatar(
                      radius: 65,
                      backgroundColor: Colors.white,
                      child: Icon(Icons.person, size: 80, color: AppColors.navyDeep),
                    ),
                  ),
                  Positioned(
                    bottom: 0,
                    right: 4,
                    child: CircleAvatar(
                      radius: 20,
                      backgroundColor: AppColors.goldMetallic,
                      child: Icon(Icons.edit, size: 20, color: AppColors.navyDeep),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),
            Text(
              authState.user?['name'] ?? 'Principal John Doe',
              style: const TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: AppColors.navyDeep),
            ),
            Text(
              authState.user?['email'] ?? 'admin@cloudischool.com',
              style: const TextStyle(fontSize: 16, color: Colors.grey),
            ),
            const SizedBox(height: 32),
            GlassCard(
              padding: const EdgeInsets.all(8),
              child: Column(
                children: [
                  _ProfileSettingItem(
                    title: 'Account Settings',
                    icon: Icons.person_outline,
                    onTap: () {},
                  ),
                  _Divider(),
                  _ProfileSettingItem(
                    title: 'Notification Preferences',
                    icon: Icons.notifications_none,
                    onTap: () {},
                  ),
                  _Divider(),
                  _ProfileSettingItem(
                    title: 'Privacy & Security',
                    icon: Icons.security_outlined,
                    onTap: () {},
                  ),
                ],
              ),
            ),
            const SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.redAccent.withOpacity(0.1),
                  foregroundColor: Colors.redAccent,
                  shadowColor: Colors.transparent,
                  side: const BorderSide(color: Colors.redAccent),
                ),
                icon: const Icon(Icons.logout_outlined),
                label: const Text('Log Out'),
                onPressed: () {
                  ref.read(authNotifierProvider.notifier).logout();
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ProfileSettingItem extends StatelessWidget {
  final String title;
  final IconData icon;
  final VoidCallback onTap;

  const _ProfileSettingItem({
    required this.title,
    required this.icon,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return ListTile(
      onTap: onTap,
      leading: Icon(icon, color: AppColors.navyDeep),
      title: Text(title, style: const TextStyle(fontWeight: FontWeight.w500)),
      trailing: const Icon(Icons.chevron_right, size: 20),
    );
  }
}

class _Divider extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Divider(height: 1, indent: 60, color: Colors.grey.withOpacity(0.1));
  }
}
