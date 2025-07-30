<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatMessage;
use App\Models\User;

class ChatController extends Controller
{
    public function getMessages()
    {
        $user = Auth::user();
        $messages = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();

        // Save user message
        $userMessage = ChatMessage::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'sender' => 'user',
            'is_read' => false
        ]);

        // Generate mentor response
        $mentorResponse = $this->generateMentorResponse($request->message);

        // Save mentor response
        $mentorMessage = ChatMessage::create([
            'user_id' => $user->id,
            'message' => $mentorResponse,
            'sender' => 'mentor',
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'user_message' => $userMessage,
            'mentor_message' => $mentorMessage
        ]);
    }

    private function generateMentorResponse($userMessage)
    {
        $message = strtolower($userMessage);
        
        $responses = [
            'deploy' => 'Untuk deploy ke Vercel, kamu bisa: 1) Push code ke GitHub, 2) Connect repository di Vercel, 3) Vercel akan otomatis build dan deploy. Sangat mudah! 🚀',
            'tips' => 'Tips belajar coding: 1) Mulai dari dasar, 2) Praktek setiap hari, 3) Buat project kecil, 4) Bergabung komunitas, 5) Jangan takut error! 💪',
            'ui ux' => 'UI (User Interface) = tampilan visual yang user lihat. UX (User Experience) = pengalaman user saat menggunakan aplikasi. UI fokus ke estetika, UX fokus ke kemudahan penggunaan! 🎨',
            'belajar' => 'Cara efektif belajar programming: 1) Pahami konsep dasar, 2) Praktek coding setiap hari, 3) Buat project real, 4) Debug dan solve problems, 5) Terus belajar teknologi baru! 📚',
            'git' => 'Git adalah sistem version control untuk tracking perubahan code. Fungsinya: 1) Menyimpan history perubahan, 2) Kolaborasi tim, 3) Backup code, 4) Branch management. Essential banget untuk developer! 🔄',
            'error' => 'Cara mengatasi error: 1) Baca error message dengan teliti, 2) Cek dokumentasi, 3) Search di Google/Stack Overflow, 4) Debug step by step, 5) Jangan panik, error adalah bagian dari belajar! 🐛'
        ];

        // Check for keywords
        if (strpos($message, 'deploy') !== false || strpos($message, 'vercel') !== false) {
            return $responses['deploy'];
        } elseif (strpos($message, 'tips') !== false || strpos($message, 'bosen') !== false || strpos($message, 'motivasi') !== false) {
            return $responses['tips'];
        } elseif (strpos($message, 'ui') !== false && strpos($message, 'ux') !== false) {
            return $responses['ui ux'];
        } elseif (strpos($message, 'belajar') !== false || strpos($message, 'efektif') !== false) {
            return $responses['belajar'];
        } elseif (strpos($message, 'git') !== false) {
            return $responses['git'];
        } elseif (strpos($message, 'error') !== false || strpos($message, 'bug') !== false) {
            return $responses['error'];
        }

        // Default responses
        $defaultResponses = [
            'Pertanyaan yang bagus! 🤔 Bisa kamu jelaskan lebih detail tentang apa yang ingin kamu tanyakan?',
            'Menarik sekali pertanyaannya! 💡 Saya akan coba bantu. Bisa kamu berikan contoh atau konteks lebih spesifik?',
            'Wah, pertanyaan yang challenging! 🚀 Saya siap membantu kamu. Apa yang sudah kamu coba sejauh ini?',
            'Pertanyaan yang sangat relevan untuk developer! 👨‍💻 Mari kita bahas bersama. Apa yang membuat kamu tertarik dengan topik ini?'
        ];

        return $defaultResponses[array_rand($defaultResponses)];
    }

    public function markAsRead()
    {
        $user = Auth::user();
        
        ChatMessage::where('user_id', $user->id)
            ->where('sender', 'mentor')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $count = ChatMessage::where('user_id', $user->id)
            ->where('sender', 'mentor')
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }
} 