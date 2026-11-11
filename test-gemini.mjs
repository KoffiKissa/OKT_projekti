import "dotenv/config";
import { GoogleGenAI } from "@google/genai";

async function main() {
    if (!process.env.GEMINI_API_KEY) {
        throw new Error("GEMINI_API_KEY is missing from .env");
    }

    console.log("API key found.");
    console.log("Initializing Gemini...");

    const ai = new GoogleGenAI({
        apiKey: process.env.GEMINI_API_KEY
    });

    console.log("Sending prompt...");
    console.log("Waiting for Gemini (30-second limit)...\n");

    const response = await ai.models.generateContent({
        model: "gemini-3.8-flash",
        contents: "Reply with exactly: Hello! Gemini is working!",
        config: {
            maxOutputTokens: 50,
            abortSignal: AbortSignal.timeout(30000)
        }
    });

    console.log("SUCCESS! Gemini responded:\n");
    console.log(response.text);

}

main().catch(error => {
    console.error("\nTEST FAILED:");
    console.error("Error name:", error.name);
    console.error("Error message:", error.message);

    if (error.status) {
        console.error("HTTP status:", error.status);
    }

    process.exitCode = 1;
});
