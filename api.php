<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// ไฟล์สำหรับเก็บข้อมูล
define('PROFILE_FILE', 'data/profile.json');
define('PROJECTS_FILE', 'data/projects.json');
define('SKILLS_FILE', 'data/skills.json');

// สร้างโฟลเดอร์ data ถ้ายังไม่มี
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

// ฟังก์ชันอ่านไฟล์ JSON
function readJSON($file) {
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

// ฟังก์ชันเขียนไฟล์ JSON
function writeJSON($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// รับ action จาก URL
$action = $_GET['action'] ?? '';

// จัดการ Request
switch ($action) {
    
    // ==================== PROFILE ====================
    case 'getProfile':
        $profile = readJSON(PROFILE_FILE);
        if (empty($profile)) {
            // ข้อมูลเริ่มต้น
            $profile = [
                'image' => 'https://img2.pic.in.th/pic/IMG_20251114_110305.jpg',
                'name' => 'เมก้า',
                'tagline' => '✨ นักเรียนผู้หลงใหลในการเขียนโค้ด',
                'description' => 'กำลังศึกษาและพัฒนาทักษะในโลกดิจิทัล พร้อมเรียนรู้เทคโนโลยีใหม่ๆ และสร้างสรรค์สิ่งที่น่าตื่นตาตื่นใจ 🚀',
                'email' => 'uoicom01@gmail.com',
                'primaryColor' => '#3B82F6',
                'secondaryColor' => '#10B981'
            ];
        }
        echo json_encode($profile);
        break;
    
    case 'saveProfile':
        $data = json_decode(file_get_contents('php://input'), true);
        if (writeJSON(PROFILE_FILE, $data)) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
        }
        break;
    
    // ==================== PROJECTS ====================
    case 'getProjects':
        $projects = readJSON(PROJECTS_FILE);
        if (empty($projects)) {
            // ข้อมูลเริ่มต้น
            $projects = [
                [
                    'image' => 'https://img2.pic.in.th/pic/Gemini_Generated_Image_kas7wskas7wskas70fa0f75ef4f13073.png',
                    'title' => '🎨 ตัวละครสไตล์ Chibi',
                    'description' => 'สร้างสรรค์ภาพตัวละครน่ารักด้วย AI และเทคนิคการออกแบบ ที่ทำให้ตัวผมกลายเป็นตัวการ์ตูนสุดน่ารัก',
                    'link' => 'https://img2.pic.in.th/pic/Gemini_Generated_Image_kas7wskas7wskas70fa0f75ef4f13073.png'
                ],
                [
                    'image' => 'https://img2.pic.in.th/pic/1763093613326.jpg',
                    'title' => '🤖 แขนหุ่นยนต์ไซเบอร์พังก์',
                    'description' => 'ผลงานการปรับแต่งภาพด้วย AI พร้อมการออกแบบแขนกลที่ดูล้ำสมัย เต็มไปด้วยรายละเอียดทางเทคโนโลยี',
                    'link' => 'https://img2.pic.in.th/pic/1763093613326.jpg'
                ]
            ];
        }
        echo json_encode($projects);
        break;
    
    case 'addProject':
        $projects = readJSON(PROJECTS_FILE);
        $newProject = json_decode(file_get_contents('php://input'), true);
        $projects[] = $newProject;
        if (writeJSON(PROJECTS_FILE, $projects)) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error']);
        }
        break;
    
    case 'updateProject':
        $projects = readJSON(PROJECTS_FILE);
        $data = json_decode(file_get_contents('php://input'), true);
        $index = $data['index'];
        unset($data['index']);
        
        if (isset($projects[$index])) {
            $projects[$index] = $data;
            if (writeJSON(PROJECTS_FILE, $projects)) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบผลงาน']);
        }
        break;
    
    case 'deleteProject':
        $projects = readJSON(PROJECTS_FILE);
        $data = json_decode(file_get_contents('php://input'), true);
        $index = $data['index'];
        
        if (isset($projects[$index])) {
            array_splice($projects, $index, 1);
            if (writeJSON(PROJECTS_FILE, $projects)) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบผลงาน']);
        }
        break;
    
    // ==================== SKILLS ====================
    case 'getSkills':
        $skills = readJSON(SKILLS_FILE);
        if (empty($skills)) {
            // ข้อมูลเริ่มต้น
            $skills = [
                'HTML5',
                'CSS3 & Animation',
                'JavaScript',
                'Python',
                'Git & GitHub',
                'Responsive Design',
                'UI/UX Design',
                'AI Integration'
            ];
        }
        echo json_encode($skills);
        break;
    
    case 'addSkill':
        $skills = readJSON(SKILLS_FILE);
        $data = json_decode(file_get_contents('php://input'), true);
        $skills[] = $data['skill'];
        if (writeJSON(SKILLS_FILE, $skills)) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error']);
        }
        break;
    
    case 'deleteSkill':
        $skills = readJSON(SKILLS_FILE);
        $data = json_decode(file_get_contents('php://input'), true);
        $index = $data['index'];
        
        if (isset($skills[$index])) {
            array_splice($skills, $index, 1);
            if (writeJSON(SKILLS_FILE, $skills)) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบทักษะ']);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
