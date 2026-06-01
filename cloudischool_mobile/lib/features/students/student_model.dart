class Student {
  final int? id;
  final String name;
  final String email;
  final String? phone;
  final String? gender;
  final String? rollNumber;
  final String? admissionNumber;
  final String? avatar;

  Student({
    this.id,
    required this.name,
    required this.email,
    this.phone,
    this.gender,
    this.rollNumber,
    this.admissionNumber,
    this.avatar,
  });

  factory Student.fromJson(Map<String, dynamic> json) {
    return Student(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
      gender: json['gender'],
      rollNumber: json['roll_number'],
      admissionNumber: json['admission_number'],
      avatar: json['avatar'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'name': name,
      'email': email,
      'phone': phone,
      'gender': gender,
      'roll_number': rollNumber,
      'admission_number': admissionNumber,
    };
  }
}
