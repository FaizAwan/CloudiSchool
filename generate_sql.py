
import random
from datetime import datetime

tenant_id = 2
school_id = 2
session_current = "April 2024 to March 2025"

first_names_male = ["Ahmed", "Ali", "Bilal", "Hamza", "Usman", "Umar", "Zaid", "Mustafa", "Abdullah", "Hassan", "Hussain", "Farhan", "Imran", "Kamran", "Salman", "Aamir", "Asad", "Fahad", "Saad", "Waqas"]
first_names_female = ["Fatima", "Ayesha", "Zainab", "Maryam", "Sana", "Sara", "Amna", "Hira", "Nida", "Kiran", "Bisma", "Esha", "Laiba", "Mahnoor", "Rimsha", "Sami", "Anaya", "Dua", "Hooriya", "Zoya"]
last_names = ["Khan", "Ahmed", "Ali", "Sheikh", "Malik", "Qureshi", "Siddiqui", "Abbasi", "Raza", "Mughal", "Butt", "Ijaz", "Javed", "Iqbal", "Shah", "Hashmi", "Gull", "Lodhi", "Farooqi", "Ansari"]
cities = ["Karachi", "Lahore", "Islamabad", "Faisalabad", "Rawalpindi", "Multan", "Peshawar", "Quetta", "Sialkot", "Gujranwala"]
sections_list = ["A", "B"]
classes = [f"Class {i}" for i in range(1, 13)]

subjects_primary = ["Urdu", "English", "Mathematics", "Islamiat", "General Science", "Social Studies"]
subjects_middle = ["Urdu", "English", "Mathematics", "Islamiat", "Science", "History", "Geography", "Computer Science"]
subjects_high = ["Urdu", "English", "Mathematics", "Islamiat", "Pakistan Studies", "Physics", "Chemistry", "Biology", "Computer Science"]

sql = ["SET FOREIGN_KEY_CHECKS = 0;"]

# Truncate tables to avoid duplicates
tables = ["fees", "students", "parents", "subjects", "feetypes", "teachers", "classes", "timetables"]
for table in tables:
    sql.append(f"TRUNCATE TABLE {table};")

# 1. Classes
vals = [f"({i}, {tenant_id}, '{name}', {school_id}, '{session_current}', 'active', NOW(), NOW())" for i, name in enumerate(classes, 1)]
sql.append(f"INSERT INTO classes (id, tenant_id, className, school_id, session, status, created_at, updated_at) VALUES {', '.join(vals)};")

# 2. Teachers
vals = []
for i in range(1, 21):
    t_name = f"{random.choice(first_names_male)} {random.choice(last_names)}"
    vals.append(f"({i}, {tenant_id}, '{t_name}', '{t_name}', 'teacher{i}@example.com', '0300{random.randint(1000000, 9999999)}', {school_id}, 'active', NOW(), NOW())")
sql.append(f"INSERT INTO teachers (id, tenant_id, teacherName, teacher_name, email, phone, school_id, status, created_at, updated_at) VALUES {', '.join(vals)};")

# 3. Feetypes
feetypes = [("Tuition Fee", "Monthly Tuition Fee"), ("Admission Fee", "One time admission fee"), ("Examination Fee", "Term examination fee")]
vals = [f"({i}, '{f[0]}', '{f[1]}', 'active', NOW(), NOW())" for i, f in enumerate(feetypes, 1)]
sql.append(f"INSERT INTO feetypes (id, name, description, status, created_at, updated_at) VALUES {', '.join(vals)};")

# 4. Subjects
subj_vals = []
sid = 1
for cid in range(1, 13):
    subjs = subjects_primary if cid <= 5 else (subjects_middle if cid <= 8 else subjects_high)
    for sname in subjs:
        subj_vals.append(f"({sid}, {tenant_id}, '{sname}', '{sname[:3].upper()}{cid:02d}', {cid}, 'active', 100, 33, NOW(), NOW())")
        sid += 1
sql.append(f"INSERT INTO subjects (id, tenant_id, subject_name, subject_code, class_id, status, total_marks, passing_marks, created_at, updated_at) VALUES {', '.join(subj_vals)};")

# 5. Parents & Students
pvals, svals, fvals = [], [], []
pid, stid = 1, 1
months = ["April", "May", "June", "July", "August", "September", "October", "November", "December", "January", "February", "March"]
for cid in range(1, 13):
    famnt = 2000 + (cid * 500)
    for sidx in range(1, 21):
        fn, mn, addr = f"{random.choice(first_names_male)} {random.choice(last_names)}", f"{random.choice(first_names_female)} {random.choice(last_names)}", f"House {random.randint(1,500)}, {random.choice(cities)}"
        ph = f"03{random.randint(0,4)}{random.randint(1,9)}-{random.randint(1000000,9999999)}"
        pvals.append(f"({pid}, {tenant_id}, '{fn}', '{fn}', '{mn}', '{ph}', 'parent{pid}@example.com', '{addr}', 'active', {school_id}, NOW(), NOW())")
        
        gen = random.choice(["Male", "Female"])
        sn = f"{random.choice(first_names_male if gen=='Male' else first_names_female)} {random.choice(last_names)}"
        dob = f"{2010+(12-cid)}-{random.randint(1,12):02d}-{random.randint(1,28):02d}"
        svals.append(f"({stid}, {tenant_id}, {cid}, '{random.choice(sections_list)}', '{sn}', 'active', {pid}, 'GR-{cid:02d}-{sidx:02d}', {school_id}, '{session_current}', '{gen}', '{dob}', '{addr}', '{ph}', NOW(), NOW())")
        
        for yr in [2024, 2025]:
            for m in months:
                status = "paid" if random.random() > 0.15 else "unpaid"
                fvals.append(f"({tenant_id}, {stid}, 1, {famnt}, '{m}', {yr}, '{status}', '{session_current}', {school_id}, 'Monthly Tuition Fee', NOW(), NOW())")
        pid, stid = pid + 1, stid + 1

sql.append(f"INSERT INTO parents (id, tenant_id, parentName, fatherName, motherName, phone, email, address, status, school_id, created_at, updated_at) VALUES {', '.join(pvals)};")
sql.append(f"INSERT INTO students (id, tenant_id, class_id, section, studentName, status, parent_id, grno, school_id, session, gender, date_of_birth, address, phone, created_at, updated_at) VALUES {', '.join(svals)};")

for i in range(0, len(fvals), 1000):
    sql.append(f"INSERT INTO fees (tenant_id, student_id, fee_type_id, fee_value, month_name, year, status, session, school_id, fee_name, created_at, updated_at) VALUES {', '.join(fvals[i:i+1000])};")

# 6. Timetables (Use INSERT IGNORE to skip duplicates)
tt_vals = []
for cid in range(1, 13):
    subjs = subjects_primary if cid <= 5 else (subjects_middle if cid <= 8 else subjects_high)
    for day in ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]:
        for p in range(1, 7):
            tt_vals.append(f"({tenant_id}, {random.randint(1,20)}, '{day}', {p}, 'Class {cid}', '{random.choice(subjs)}', NOW(), NOW())")
sql.append(f"INSERT IGNORE INTO timetables (tenant_id, teacher_id, day, period_id, class, subject, created_at, updated_at) VALUES {', '.join(tt_vals)};")

sql.append("SET FOREIGN_KEY_CHECKS = 1;")
with open("dummy_data.sql", "w") as f: f.write("\n".join(sql))
