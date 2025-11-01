<?php
/**
 * File: deliverEventObject.php
 * Purpose: Fetch a single event record from the database and return it as JSON.
 * Summary:
 *   - Loads a shared PDO connection from dbConnect1.php.
 *   - Queries table `wdv341_events` for one row by events_id (currently hard-coded to 1).
 *   - Maps the result to an Event DTO and outputs application/json.
 * Inputs:
 *   - (Optional) Intended id source: query string `?id=<int>` (not implemented here; change hard-coded id if needed).
 * Dependencies: Requires dbConnect1.php to define $pdo (PDO instance).
 * Last updated: 2025-11-01
 */
require 'dbConnect1.php'; // Use the shared PDO connection

// Prepare and execute SELECT statement
$sql = "SELECT events_id, events_name, events_description, events_presenter, events_date, events_time FROM wdv341_events WHERE events_id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => 1]); // Change 1 to desired event id
$row = $stmt->fetch();

// Event class definition
class Event {
	private $events_id;
	private $events_name;
	private $events_description;
	private $events_presenter;
	private $events_date;
	private $events_time;

	// Getters
	public function getEventsId() { return $this->events_id; }
	public function getEventsName() { return $this->events_name; }
	public function getEventsDescription() { return $this->events_description; }
	public function getEventsPresenter() { return $this->events_presenter; }
	public function getEventsDate() { return $this->events_date; }
	public function getEventsTime() { return $this->events_time; }

	// Setters
	public function setEventsId($id) { $this->events_id = $id; }
	public function setEventsName($name) { $this->events_name = $name; }
	public function setEventsDescription($desc) { $this->events_description = $desc; }
	public function setEventsPresenter($presenter) { $this->events_presenter = $presenter; }
	public function setEventsDate($date) { $this->events_date = $date; }
	public function setEventsTime($time) { $this->events_time = $time; }

	// Convert object to array for json_encode
	public function toArray() {
		return [
			'events_id' => $this->getEventsId(),
			'events_name' => $this->getEventsName(),
			'events_description' => $this->getEventsDescription(),
			'events_presenter' => $this->getEventsPresenter(),
			'events_date' => $this->getEventsDate(),
			'events_time' => $this->getEventsTime()
		];
	}
}

header('Content-Type: application/json');
// Map DB row to Event object
$outputObj = new Event();
if ($row) {
	$outputObj->setEventsId($row['events_id']);
	$outputObj->setEventsName($row['events_name']);
	$outputObj->setEventsDescription($row['events_description']);
	$outputObj->setEventsPresenter($row['events_presenter']);
	$outputObj->setEventsDate($row['events_date']);
	$outputObj->setEventsTime($row['events_time']);
	echo json_encode($outputObj->toArray());
} else {
	echo json_encode(["error" => "Event not found."]);
}
