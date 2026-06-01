# Implementation Plan - CloudiSchool Mobile (Flutter)

## 📋 Overview
Create an ultra-premium, production-ready Flutter mobile application for **CloudiSchool**. The app will feature a cinematic UI based on the **Stitch** design system, robust state management with **Riverpod**, and advanced AI features using **Gemini Pro**.

## 🎯 Success Criteria
- [ ] Pixel-perfect implementation of 15+ screens.
- [ ] Cinematic animations (Lottie + Hero + Shimmer).
- [ ] Full integration with Laravel API (`https://cloudischool.com`).
- [ ] AI Creative Studio (Gemini + Veo 3) fully functional.
- [ ] Production-ready code (Clean Architecture, Secure Storage, Material 3).

## 🛠️ Tech Stack
- **Framework**: Flutter 3.24+ (Latest Stable)
- **UI/UX**: Material 3 + Custom Stitch Theme (Navy/Gold/Glassmorphism)
- **State Management**: Riverpod 2.5+
- **Networking**: Dio 5.7+ (Interceptors, Auth Header, Error Handling)
- **AI**: `google_generative_ai` (Gemini Pro) + `veo_3_nano_banana` integration
- **Storage**: `flutter_secure_storage` (Auth tokens), `shared_preferences` (Settings)
- **Architecture**: Feature-First Clean Architecture

## 📁 Project Structure (Planned)
```plaintext
cloudischool_mobile/
├── lib/
│   ├── main.dart
│   ├── app.dart                   # Main App Configuration
│   ├── core/                      # Global utilities, theme, and networking
│   │   ├── api/                   # Dio client & Interceptors
│   │   ├── theme/                 # Stitch Design System (AppTheme, AppColors)
│   │   ├── constants/             # App constants (base URL, keys)
│   │   └── widgets/               # Universal UI components (GlassCard, StitchButton)
│   ├── features/                  # Feature-based modular structure
│   │   ├── auth/                  # Login, OTP, Forgot Password
│   │   ├── dashboard/             # Stats & Quick Access
│   │   ├── students/              # Student CRUD
│   │   ├── teachers/              # Teacher CRUD
│   │   ├── parents/               # Parent CRUD
│   │   ├── exams/                 # Manual Exam Marks Entry
│   │   ├── fees/                  # Fee Management
│   │   ├── attendance/            # Attendance Tracking
│   │   ├── profile/               # User Settings & Profile
│   │   └── ai_studio/             # Gemini & Veo integration
│   └── shared/                    # Shared models & providers
```

## 📝 Task Breakdown

### Phase 1: Foundation (P0)
| Task ID | Component | Agent | Description | Verify |
|---------|-----------|-------|-------------|--------|
| T1.1 | Scaffolding | `project-planner` | Initialize Flutter project in `mobile/` directory and set up `pubspec.yaml` | `flutter pub get` success |
| T1.2 | Design System | `mobile-developer` | Implement **AppTheme** with Stitch Navy/Gold palette, Typography, and Glassmorphism styles | Visual check of theme colors |
| T1.3 | Networking | `mobile-developer` | Set up **Dio** client with Laravel Sanctum interceptors and secure storage for tokens | Logger prints API responses |

### Phase 2: Auth & Core Screens (P1)
| Task ID | Component | Agent | Description | Verify |
|---------|-----------|-------|-------------|--------|
| T2.1 | Splash/Onboard | `mobile-developer` | Create animated Splash and premium Onboarding slides | Smooth transitions + Lottie playback |
| T2.2 | Auth Flow | `mobile-developer` | Implement Login, OTP Registration, and Password Reset screens | Auth token stored in SecureStorage |
| T2.3 | Dashboard | `mobile-developer` | Implement Main Dashboard with stats from `/dashboard/stats` | Stats cards render dynamic data |

### Phase 3: Feature Implementation (P2)
| Task ID | Component | Agent | Description | Verify |
|---------|-----------|-------|-------------|--------|
| T3.1 | CRUD Features | `mobile-developer` | Build Students, Teachers, and Parents management screens (List/Add/Edit/Delete) | All CRUD operations reflect on UI |
| T3.2 | Exams/Fees | `mobile-developer` | Implement Manual Exams grid and Fee Management overview | Batch saving marks works |
| T3.3 | Attendance | `mobile-developer` | Build Attendance calendar and student marking list | Color feedback updates on tap |

### Phase 4: AI & Polish (P3)
| Task ID | Component | Agent | Description | Verify |
|---------|-----------|-------|-------------|--------|
| T4.1 | AI Assistant | `mobile-developer` | Integrate Gemini Pro FAB for contextual AI help on all screens | Chat interface returns AI responses |
| T4.2 | Creative Studio | `mobile-developer` | Implement "Veo 3 Nano Banana" quickstart for generative school posters | Images generated from prompt |
| T4.3 | Optimization | `performance-optimizer` | Run memory audit, optimize images, and ensure 60FPS animations | No jank on low-end devices |

---

## ⏸️ Phase X: Verification Checklist
- [ ] `security_scan.py`: Pass (No leaked API keys)
- [ ] `ux_audit.py`: Pass (Check Fitts' Law and visual hierarchy)
- [ ] `mobile_audit.py`: Pass (Touch targets and performance)
- [ ] Full Build: `flutter build apk/appbundle` success
