<?php

namespace App\SharedKernel\Presentation\Web\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for reading and displaying log files through web interface
 * Provides secure access to application logs with filtering and download capabilities
 */
#[Route('/admin/logs')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class LogReaderController extends AbstractController
{
    /**
     * Display the last N lines of a log file
     *
     * @param Request $request HTTP request object
     * @param int $lines Number of lines to display (default: 100, max: 1000)
     * @return Response HTML or JSON response with log content
     * @throws Exception
     */
    #[Route('/{lines}', name: 'admin_logs', defaults: ['lines' => 100], methods: ['GET'])]
    public function readLogs(Request $request, int $lines = 100): Response
    {
        // Limit the number of lines to prevent abuse and memory issues
        $lines = min(max($lines, 1), 1000);

        // Get log type from query parameters
        $logType = $request->query->get('type', 'app');

        // Define allowed log files for security (whitelist approach)
        $allowedLogs = [
            'app' => $this->getParameter('kernel.logs_dir') . '/' . $this->getParameter('kernel.environment') . '.log',
            'doctrine' => $this->getParameter('kernel.logs_dir') . '/doctrine.log',
            'deprecation' => $this->getParameter('kernel.logs_dir') . '/deprecation.log',
        ];


        // Validate that the requested log type is allowed
        if (!isset($allowedLogs[$logType])) {
            throw $this->createNotFoundException('Unauthorized log type');
        }

        $logFile = $allowedLogs[$logType];

        // Check if the log file exists
        if (!file_exists($logFile)) {
            return new Response("Log file does not exist: " . basename($logFile), 404);
        }

        // Read the last N lines of the file efficiently
        $logContent = $this->readLastLines($logFile, $lines);

        // Determine response format (HTML or JSON)
        $format = $request->query->get('format', 'html');

        if ($format === 'json') {
            return $this->json([
                'file' => basename($logFile),
                'lines' => $lines,
                'content' => $logContent
            ]);
        }

        // Return HTML format by default with template rendering
        return $this->render('@SharedKernel/admin/logs.html.twig', [
            'logContent' => $logContent,
            'logType' => $logType,
            'lines' => $lines,
            'availableLogs' => array_keys($allowedLogs),
            'fileSize' => $this->formatBytes(filesize($logFile)),
            'lastModified' => new \DateTime('@' . filemtime($logFile))
        ]);
    }

    /**
     * Download complete log file
     *
     * @param string $type Type of log file to download
     * @return Response File download response
     */
    #[Route('/download/{type}', name: 'admin_logs_download', methods: ['GET'])]
    public function downloadLog(string $type): Response
    {
        // Same whitelist as above for security
        $allowedLogs = [
            'app' => $this->getParameter('kernel.logs_dir') . '/' . $this->getParameter('kernel.environment') . '.log',
            'doctrine' => $this->getParameter('kernel.logs_dir') . '/doctrine.log',
            'deprecation' => $this->getParameter('kernel.logs_dir') . '/deprecation.log',
        ];

        if (!isset($allowedLogs[$type])) {
            throw $this->createNotFoundException('Unauthorized log type');
        }

        $logFile = $allowedLogs[$type];

        if (!file_exists($logFile)) {
            throw $this->createNotFoundException('Log file not found');
        }

        // Use Symfony's file() method to serve the file for download
        return $this->file($logFile, basename($logFile));
    }

    /**
     * Efficiently read the last N lines of a file without loading the entire file
     * Uses reverse reading technique to minimize memory usage on large files
     *
     * @param string $filename Path to the file to read
     * @param int $lines Number of lines to read from the end
     * @return array<string> Array of lines (strings)
     */
    private function readLastLines(string $filename, int $lines): array
    {
        $handle = fopen($filename, 'r');

        if (!$handle) {
            return ['Error: Unable to open file'];
        }

        // Move to end of file to get file size
        fseek($handle, 0, SEEK_END);
        $filesize = ftell($handle);

        if ($filesize == 0) {
            fclose($handle);
            return ['File is empty'];
        }

        $buffer = '';
        $pos = $filesize;
        $linesFound = 0;

        // Read file backwards in chunks to find the required number of lines
        while ($pos > 0 && $linesFound < $lines) {
            // Read in 4KB chunks for efficiency
            $chunkSize = min(4096, $pos);
            $pos -= $chunkSize;

            fseek($handle, $pos);
            $chunk = fread($handle, $chunkSize);
            $buffer = $chunk . $buffer;

            // Count newlines in the current buffer
            $linesFound = substr_count($buffer, "\n");
        }

        fclose($handle);

        // Split buffer into lines
        $allLines = explode("\n", $buffer);

        // Remove empty last line if it exists
        if (end($allLines) === '') {
            array_pop($allLines);
        }

        // Take only the last N lines
        $lastLines = array_slice($allLines, -$lines);

        return $lastLines;
    }

    /**
     * Format file size in human-readable format
     * Converts bytes to appropriate unit (B, KB, MB, GB)
     *
     * @param int $bytes File size in bytes
     * @return string Formatted file size with unit
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
