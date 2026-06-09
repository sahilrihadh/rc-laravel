<?php
// app/Http/Controllers/PageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Poll;
use App\Models\PollVote;
use App\Models\Reaction;
use App\Models\PreviousSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateMail;
use App\Events\PollStatusChanged;

class PageController extends Controller
{
    // Remove the constructor completely

    // Webcast Page
    public function webcast()
    {
        $user = Auth::user();
        return view('pages.webcast', compact('user'));
    }

    // Previous Sessions Page
    public function previousSessions()
    {
        $faqs = [
            ['question' => 'What is the drug of choice for pancreatitis?', 'answer' => '<p>There are no universally recommended drugs except <strong>Fuzapladib sodium monohydrate</strong>, currently licensed in Japan and conditionally approved in the USA.</p><p>Most cases are managed symptomatically for abdominal pain, hypovolemia/shock, gastrointestinal signs, dietary therapy, and monitoring for complications.</p><p class="mb-0">Therapy should be individualized and customized for each case.</p>'],

            ['question' => 'Can you explain the cytokine storm in acute pancreatitis?', 'answer' => '<p>The inflammatory response in acute pancreatitis (AP) is complex and may be localized or systemic, determining disease severity and progression.</p><p>Release of pancreatic enzymes leads to neutrophilic inflammation, production of reactive oxygen species, nitric oxide, cytokines, and activation of multiple inflammatory pathways.</p><p>Key mechanisms include:</p><ul><li>Trypsinogen activation</li><li>Activation of nuclear factor kappa B (NF-κB)</li><li>Release of inflammatory mediators</li><li>Interleukin-6 (IL-6) production</li><li>Neutrophil invasion</li></ul><p class="mb-0">Refer to <strong>Cridge et al</strong> and <strong>Mansfield et al (JVIM)</strong> for detailed review.</p>'],

            ['question' => 'Can we use SNAP cPL of dogs for cats if fPL is unavailable?', 'answer' => '<p><strong>Unlikely and not validated.</strong></p><p class="mb-0">The feline pancreatic lipase immunoreactivity (fPLI) assay is one of the most sensitive and specific tests for feline pancreatitis.</p>'],

            ['question' => 'What are the limitations of ultrasonography in early pancreatitis?', 'answer' => '<p class="mb-0">The pancreas may appear normal in the early stages before morphological ultrasonographic changes develop. Monitor closely if pancreatitis is strongly suspected, especially with elevated cPL values.</p>'],

            ['question' => 'Is there any benefit of adding oral pancreatic enzymes in pancreatitis?', 'answer' => '<p class="mb-0"><strong>Unlikely.</strong> Additional oral pancreatic enzymes generally do not provide benefit unless the patient develops exocrine pancreatic insufficiency (EPI).</p>'],

            ['question' => 'Is a low-fat GI diet suitable for EPI too?', 'answer' => '<p class="mb-0"><strong>Yes.</strong> A low-fat gastrointestinal diet can also be suitable for EPI patients.</p>'],

            ['question' => 'Dog is epileptic and on Gardenal. Should treatment continue during acute pancreatitis?', 'answer' => '<p>Phenobarbitone\'s direct role in causing acute pancreatitis is not yet proven and may instead be associated with obesity or polyphagia.</p><p class="mb-0">Alternatives like <strong>levetiracetam</strong> may be considered until pancreatitis resolves, but generally <strong>Gardenal is not stopped abruptly.</strong></p>'],

            ['question' => 'How can we differentiate GERD from pancreatitis in dogs?', 'answer' => '<ul><li>GERD is less common in dogs compared to humans</li><li>GERD patients generally do not appear severely ill</li><li>Regurgitation is more common than vomiting</li><li>Systemic signs are uncommon in GERD</li><li>Blood work is usually normal unless chronic disease exists</li></ul><p class="mb-0">Acute pancreatitis patients usually appear significantly sicker.</p>'],

            ['question' => 'Is NAC good to give IV for acute pancreatitis?', 'answer' => '<p class="mb-0">Not validated, but generally considered safe if clinically indicated.</p>'],

            ['question' => 'Can freeze-dried raw pancreas and cobalamin be used for AP?', 'answer' => '<p>Excellent results are reported for <strong>EPI management</strong>.</p><p class="mb-0">However, this approach is generally <strong>not recommended for acute pancreatitis (AP).</strong></p>'],

            ['question' => 'Management approach for epilepsy patients on high-fat diets who develop pancreatitis?', 'answer' => '<p>Acute pancreatitis should be managed first until recovery.</p><p class="mb-0">High-fat diets are not always essential for epilepsy management. Alternatives such as MCT oil or coconut oil may be considered.</p>'],

            ['question' => 'How do you manage babesiosis-induced acute pancreatitis?', 'answer' => '<p>Treat babesiosis with recommended specific therapy while simultaneously managing acute pancreatitis symptomatically.</p><p class="mb-0">Control inflammation, shock, pain, and dehydration. Drug conflicts affecting the pancreas are considered unlikely.</p>'],

            ['question' => 'What is the most underestimated mechanism driving morbidity in pancreatitis?', 'answer' => '<p>Traditional theories focus on trypsin activation, while newer concepts emphasize cytokine storm pathways.</p><p>No single chemokine or cytokine appears solely responsible. It is a cascade of inflammatory events triggered by NF-κB and related mediators.</p><p class="mb-0">Refer to <strong>Cridge et al</strong> and <strong>Mansfield et al (JVIM)</strong> for detailed review.</p>']
        ];

        return view('pages.previous-sessions', compact('faqs'));
    }

    public function sendCertificate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'fullName' => 'required|string',
            'webinarId' => 'required|string'
        ]);

        $email = $request->email;
        $webinarId = $request->webinarId;
        $fullName = $request->fullName;

        // Check if certificate already sent
        $existing = PreviousSession::where('email_id', $email)
            ->where('session_name', $webinarId)
            ->first();

        if ($existing && $existing->certificate_status == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate already sent for this webinar',
                'already_sent' => true
            ]);
        }

        // Define webinar configurations
        $webinarConfig = [
            'webinar1' => ['event_date' => '27th May 2026', 'template' => 'assets/img/Certificate1.png'],
            'webinar2' => ['event_date' => '10th November 2026', 'template' => 'assets/img/Certificate_10_November.png'],
            'webinar3' => ['event_date' => '24th November 2026', 'template' => 'assets/img/Certificate_24_November.png'],
            'webinar4' => ['event_date' => '15th October 2026', 'template' => 'assets/img/Certificate_15_Oct.png'],
            'webinar5' => ['event_date' => '26th November 2026', 'template' => 'assets/img/Certificate_26_Nov.png'],
        ];

        if (!isset($webinarConfig[$webinarId])) {
            return response()->json(['success' => false, 'message' => 'Invalid webinar ID'], 400);
        }

        // Generate certificate image
        $certificatePath = $this->generateCertificateImage($fullName, $webinarConfig[$webinarId]['template']);

        if (!$certificatePath) {
            return response()->json(['success' => false, 'message' => 'Failed to generate certificate'], 500);
        }

        // Save/Update record
        if ($existing) {
            $existing->update([
                'certificate_status' => 1,
                'certificate_path' => $certificatePath,
                'count' => $existing->count + 1
            ]);
        } else {
            PreviousSession::create([
                'name' => $fullName,
                'email_id' => $email,
                'session_name' => $webinarId,
                'watched_on' => now(),
                'certificate_status' => 1,
                'certificate_path' => $certificatePath,
                'count' => 1
            ]);
        }

        // Send email
        try {
            Mail::to($email)->send(new CertificateMail($fullName, $webinarConfig[$webinarId]['event_date'], $certificatePath));

            return response()->json([
                'success' => true,
                'message' => 'Certificate sent successfully to ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateCertificateImage($fullName, $templatePath)
    {
        try {
            // Log the attempt
            Log::info('Starting certificate generation', [
                'fullName' => $fullName,
                'templatePath' => $templatePath
            ]);

            // Check if template exists
            $fullTemplatePath = public_path($templatePath);
            Log::info('Looking for template at: ' . $fullTemplatePath);

            if (!file_exists($fullTemplatePath)) {
                Log::error("Template not found: {$fullTemplatePath}");
                return false;
            }

            // Load template image
            $templateImage = imagecreatefrompng($fullTemplatePath);
            if (!$templateImage) {
                Log::error("Failed to create image from template");
                return false;
            }

            // Check font file
            $fontFile = public_path('assets/fonts/D-DIN-PRO-500-Medium.otf');
            Log::info('Looking for font at: ' . $fontFile);

            if (!file_exists($fontFile)) {
                // Try alternative font paths
                $alternativeFonts = [
                    public_path('assets/fonts/D-DIN-PRO-Medium.otf'),
                    public_path('assets/fonts/D-DIN-PRO-Bold.otf'),
                    public_path('assets/fonts/arial.ttf'),
                    public_path('assets/fonts/Roboto-Regular.ttf')
                ];

                $fontFound = false;
                foreach ($alternativeFonts as $altFont) {
                    if (file_exists($altFont)) {
                        $fontFile = $altFont;
                        $fontFound = true;
                        Log::info('Using alternative font: ' . $fontFile);
                        break;
                    }
                }

                if (!$fontFound) {
                    Log::error("No font file found");
                    imagedestroy($templateImage);
                    return false;
                }
            }

            // Set font color (black)
            $fontColor = imagecolorallocate($templateImage, 0, 0, 0);

            // Add text to image
            $result = imagettftext($templateImage, 46, 0, 900, 850, $fontColor, $fontFile, $fullName);

            if (!$result) {
                Log::error("Failed to add text to image");
                imagedestroy($templateImage);
                return false;
            }

            // Create certificates directory if not exists
            $certificateDir = storage_path('app/public/certificates');
            if (!file_exists($certificateDir)) {
                $created = mkdir($certificateDir, 0777, true);
                if (!$created) {
                    Log::error("Failed to create directory: {$certificateDir}");
                    imagedestroy($templateImage);
                    return false;
                }
            }

            // Save certificate
            $fileName = time() . '_' . rand(1000, 9999) . '.png';
            $fullPath = $certificateDir . '/' . $fileName;

            $saved = imagepng($templateImage, $fullPath);
            imagedestroy($templateImage);

            if (!$saved) {
                Log::error("Failed to save image: {$fullPath}");
                return false;
            }

            Log::info('Certificate generated successfully: ' . $fileName);
            return 'certificates/' . $fileName;
        } catch (\Exception $e) {
            Log::error('Certificate generation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    // Player Page (Video Stream)
    public function player()
    {
        return view('pages.player');
    }

    // Rest of your methods remain the same...
    public function submitQuestion(Request $request)
    {
        // Your existing code
    }

    public function getQuestions()
    {
        // Your existing code
    }

    public function checkPoll(Request $request)
    {
        // Your existing logic to check poll from database
        // This could be checking a 'polls' table
        $activePoll = $this->getActivePoll(); // Your logic to get active poll

        if ($activePoll) {
            return response()->json($activePoll); // Return poll HTML/data
        }

        return response()->json('NO_POLL_ACTIVE');
    }

    // Admin will call this to activate a poll
    public function activatePoll(Request $request)
    {
        $pollId = $request->poll_id;

        // Activate poll in database
        // Poll::where('id', $pollId)->update(['is_active' => true]);

        // Broadcast to all connected clients
        $pollData = $this->getPollHtml($pollId); // Get poll HTML
        broadcast(new PollStatusChanged($pollData));

        return response()->json(['success' => true]);
    }

    // Admin will call this to deactivate a poll
    public function deactivatePoll(Request $request)
    {
        // Deactivate poll in database

        // Broadcast to all connected clients
        broadcast(new PollStatusChanged(null));

        return response()->json(['success' => true]);
    }

    public function submitVote(Request $request)
    {
        // Your existing code
    }

    public function storeReaction(Request $request)
    {
        // Your existing code
    }

    public function trackActivity(Request $request)
    {
        // Your existing code
    }
}
