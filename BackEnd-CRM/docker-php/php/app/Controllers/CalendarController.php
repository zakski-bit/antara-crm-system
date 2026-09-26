<?php

namespace App\Controllers;

use App\Core\Database\Connection;
use PDO;

class CalendarController
{
    /**
     * Fetch calendar events.
     *
     * @return void
     */
    public function index(): void
    {
        $pdo = Connection::getInstance();
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $categories = $_GET['categories'] ?? [];

        if (!$start || !$end) {
            http_response_code(400);
            echo json_encode(['error' => 'Start and end parameters are required.']);
            return;
        }

        $sql = "SELECT id, title, description, location, start_date, start_time, end_date, end_time, all_day, background_color, text_color, class_name FROM calendar_events WHERE start_date >= :start AND end_date <= :end";
        
        if (!empty($categories) && is_array($categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $sql .= " AND class_name IN ($placeholders)";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':start', $start);
        $stmt->bindValue(':end', $end);

        if (!empty($categories) && is_array($categories)) {
            foreach ($categories as $k => $category) {
                $stmt->bindValue(($k + 3), $category);
            }
        }
        
        $stmt->execute();
        $databaseEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = array_map(function ($event) {
            return $this->formatEvent($event);
        }, $databaseEvents);

        header('Content-Type: application/json');
        echo json_encode(['events' => $events]);
    }

    /**
     * Store a new event.
     *
     * @return void
     */
    public function store(): void
    {
        $payload = (array) json_decode(file_get_contents('php://input'));
        
        $sql = "INSERT INTO calendar_events (title, description, location, start_date, start_time, end_date, end_time, all_day, background_color, text_color, class_name, created_at, updated_at) VALUES (:title, :description, :location, :start_date, :start_time, :end_date, :end_time, :all_day, :background_color, :text_color, :class_name, NOW(), NOW())";
        
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':title' => $payload['title'] ?? 'Untitled Event',
            ':description' => $payload['description'] ?? null,
            ':location' => $payload['location'] ?? null,
            ':start_date' => $payload['start_date'],
            ':start_time' => !empty($payload['start_time']) ? $payload['start_time'] : null,
            ':end_date' => !empty($payload['end_date']) ? $payload['end_date'] : $payload['start_date'],
            ':end_time' => !empty($payload['end_time']) ? $payload['end_time'] : null,
            ':all_day' => $payload['all_day'] ? 1 : 0,
            ':background_color' => $payload['background_color'] ?? null,
            ':text_color' => $payload['text_color'] ?? null,
            ':class_name' => $payload['class_name'] ?? 'primary'
        ]);

        $id = $pdo->lastInsertId();
        $selectStmt = $pdo->prepare("SELECT * FROM calendar_events WHERE id = :id");
        $selectStmt->execute([':id' => $id]);
        $newEvent = $selectStmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['event' => $this->formatEvent($newEvent)]);
    }

    /**
     * Update an existing event.
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void
    {
        $payload = (array) json_decode(file_get_contents('php://input'));

        $sql = "UPDATE calendar_events SET title = :title, description = :description, location = :location, start_date = :start_date, start_time = :start_time, end_date = :end_date, end_time = :end_time, all_day = :all_day, background_color = :background_color, text_color = :text_color, class_name = :class_name, updated_at = NOW() WHERE id = :id";

        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':title' => $payload['title'] ?? 'Untitled Event',
            ':description' => $payload['description'] ?? null,
            ':location' => $payload['location'] ?? null,
            ':start_date' => $payload['start_date'],
            ':start_time' => !empty($payload['start_time']) ? $payload['start_time'] : null,
            ':end_date' => !empty($payload['end_date']) ? $payload['end_date'] : $payload['start_date'],
            ':end_time' => !empty($payload['end_time']) ? $payload['end_time'] : null,
            ':all_day' => $payload['all_day'] ? 1 : 0,
            ':background_color' => $payload['background_color'] ?? null,
            ':text_color' => $payload['text_color'] ?? null,
            ':class_name' => $payload['class_name'] ?? 'primary'
        ]);

        $selectStmt = $pdo->prepare("SELECT * FROM calendar_events WHERE id = :id");
        $selectStmt->execute([':id' => $id]);
        $updatedEvent = $selectStmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['event' => $this->formatEvent($updatedEvent)]);
    }

    /**
     * Delete an event.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $sql = "DELETE FROM calendar_events WHERE id = :id";
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    /**
     * Format event for FullCalendar.
     *
     * @param array $event
     * @return array
     */
    private function formatEvent(array $event): array
    {
        $start = $event['start_date'];
        if ($event['start_time']) {
            $start .= 'T' . $event['start_time'];
        }

        $end = $event['end_date'];
        if ($event['end_time']) {
            $end .= 'T' . $event['end_time'];
        } elseif ($event['all_day']) {
            // For all-day events, FullCalendar expects the end date to be exclusive.
            // So, if an event is for one day, end date should be the next day.
            $endDate = new \DateTime($event['end_date'] ?: $event['start_date']);
            $endDate->modify('+1 day');
            $end = $endDate->format('Y-m-d');
        }

        return [
            'id' => $event['id'],
            'title' => $event['title'],
            'start' => $start,
            'end' => $end,
            'allDay' => (bool)$event['all_day'],
            'className' => $event['class_name'],
            'backgroundColor' => $event['background_color'],
            'textColor' => $event['text_color'],
            'extendedProps' => [
                'description' => $event['description'],
                'location' => $event['location'],
            ],
        ];
    }
}