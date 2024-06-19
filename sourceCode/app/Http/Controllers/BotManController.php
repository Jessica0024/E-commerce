<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\Services\OpenAI;
use Exception;

class BotManController extends Controller
{
    /**
     * Handle user's incoming message.
     *
     * @return void
     */
    public function handle(Request $request)
    {
        $botman = app('botman');

        // Listen for incoming messages
        $botman->hears('.*', function (BotMan $bot) use ($request) {
            // Get user's message
            $message = $request->input('message');

            try {
                // Call OpenAI API to generate response
                $response = $this->generateResponse($message);

                // Reply to user with the generated response
                $bot->reply($response);
            } catch (Exception $e) {
                // Handle any errors
                $bot->reply("Sorry, something went wrong while processing your request.");
            }
        });

        // Start listening
        $botman->listen();
    }

    /**
     * Generate response using OpenAI API.
     *
     * @param string $message
     * @return string
     */
    private function generateResponse($message)
    {
        // Set up OpenAI API client
        $openai = new OpenAI([
            'api_key' => config('services.openai.api_key'),
            'default_model' => 'davinci', // Choose the appropriate model
        ]);

        // Call OpenAI API to generate response
        $response = $openai->complete([
            'prompt' => $message,
            'max_tokens' => 50, // Adjust max_tokens as needed
        ]);

        // Extract the generated response from the API response
        $generatedResponse = $response['choices'][0]['text'] ?? '';

        return $generatedResponse;
    }
}
