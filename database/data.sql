-- Seed data for a fresh database created with tables.sql.
-- The sample tours contain four and three objects to exercise both rank ranges.

INSERT INTO Objects (name, description, extended_description) VALUES
('The Rosetta Stone', 'An ancient Egyptian granodiorite stele inscribed with three versions of a decree.', 'Discovered in 1799, it holds the key to understanding Egyptian hieroglyphs.'),
('Apollo 11 Command Module', 'The spacecraft that carried the first humans to the Moon.', 'Columbia is the only part of the Apollo 11 spacecraft that returned to Earth intact.'),
('Tyrannosaurus Rex Skeleton', 'A nearly complete fossilized skeleton of a T-Rex.', 'Excavated in the late 20th century, it stands over 12 feet tall at the hips.'),
('Starry Night', 'Famous oil on canvas painting by Vincent van Gogh.', 'Painted in June 1889, it depicts the view from the east-facing window of his asylum room.'),
('Ancient Greek Astrolabe', 'An instrument used to model the sky and solve astronomical problems.', 'Astrolabes helped astronomers and navigators locate celestial objects.');

INSERT INTO Objects_Images (object_id, image_url) VALUES
(1, 'https://picsum.photos/id/1015/400/300'),
(2, 'https://picsum.photos/id/1025/400/300'),
(3, 'https://picsum.photos/id/1035/400/300'),
(4, 'https://picsum.photos/id/1045/400/300'),
(4, 'https://picsum.photos/id/1055/400/300'),
(5, 'https://picsum.photos/id/1065/400/300');

INSERT INTO Tours (name, description) VALUES
('Museum Highlights Tour', 'A quick walkthrough of the museum''s most famous artifacts and exhibits.'),
('Space and Science Tour', 'Explore objects connected to science, astronomy, and space exploration.');

-- Tour 1 has four objects at unique positions 1 through 4.
-- Tour 2 has three objects at unique positions 1 through 3.
INSERT INTO Tours_Objects (tour_id, position, object_id) VALUES
(1, 1, 1),
(1, 2, 4),
(1, 3, 2),
(1, 4, 3),
(2, 1, 5),
(2, 2, 2),
(2, 3, 3);

-- Replace these placeholder URLs with the real archetype interactive links.
INSERT INTO Archetypes (name, icon, interactive) VALUES
('The Scholar', 'scholar.png', 'https://example.com/interactives/scholar'),
('The Explorer', 'explorer.png', 'https://example.com/interactives/explorer'),
('The Casual Visitor', 'casual-visitor.png', 'https://example.com/interactives/casual-visitor');

-- Each archetype ranks every object in each tour exactly once, from N down to 1.
-- Tour 1 has N=4; Tour 2 has N=3.
INSERT INTO Archetype_Interest (archetype, object, tour, interest, position) VALUES
-- The Scholar: Tour 1
('The Scholar', 1, 1, 4, 1),
('The Scholar', 4, 1, 3, 2),
('The Scholar', 2, 1, 2, 3),
('The Scholar', 3, 1, 1, 4),
-- The Scholar: Tour 2
('The Scholar', 5, 2, 3, 1),
('The Scholar', 2, 2, 2, 2),
('The Scholar', 3, 2, 1, 3),
-- The Explorer: Tour 1
('The Explorer', 2, 1, 4, 3),
('The Explorer', 3, 1, 3, 4),
('The Explorer', 1, 1, 2, 1),
('The Explorer', 4, 1, 1, 2),
-- The Explorer: Tour 2
('The Explorer', 2, 2, 3, 2),
('The Explorer', 5, 2, 2, 1),
('The Explorer', 3, 2, 1, 3),
-- The Casual Visitor: Tour 1
('The Casual Visitor', 4, 1, 4, 2),
('The Casual Visitor', 3, 1, 3, 4),
('The Casual Visitor', 2, 1, 2, 3),
('The Casual Visitor', 1, 1, 1, 1),
-- The Casual Visitor: Tour 2
('The Casual Visitor', 3, 2, 3, 3),
('The Casual Visitor', 5, 2, 2, 1),
('The Casual Visitor', 2, 2, 1, 2);

INSERT INTO Users (id, is_guest) VALUES
('550e8400-e29b-41d4-a716-446655440000', 1),
('a3b8c4d2-f9e1-4589-b271-9c8d7e6f5a4b', 1),
('c8f9b2a1-e4d7-4632-9018-f2b3c4d5e6f7', 1);

-- User 1 has one completed session and one active session; only one is active.
INSERT INTO Sessions (user_id, created_at, active, tour_id, archetype) VALUES
('550e8400-e29b-41d4-a716-446655440000', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 2 HOUR), NULL, 1, 'The Scholar'),
('550e8400-e29b-41d4-a716-446655440000', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE), 1, 2, 'The Explorer'),
('a3b8c4d2-f9e1-4589-b271-9c8d7e6f5a4b', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 30 MINUTE), 1, 1, 'The Casual Visitor'),
('c8f9b2a1-e4d7-4632-9018-f2b3c4d5e6f7', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY), NULL, 2, 'The Scholar');

INSERT INTO Sessions_Pictures (session_id, image_url) VALUES
(1, 'https://picsum.photos/id/1075/400/300');

INSERT INTO Sessions_Feedback (session_id, feedback_text) VALUES
(1, 'The tour was informative and easy to follow.');

-- Stops reference objects that belong to each session's tour.
INSERT INTO Stops (session_id, object_id, time_of_stop, feedback_text) VALUES
-- Completed highlights tour
(1, 1, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 115 MINUTE), 'Fascinating to see the real translation key.'),
(1, 4, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 100 MINUTE), 'The colors are much more vibrant in person.'),
(1, 2, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 85 MINUTE), NULL),
(1, 3, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 70 MINUTE), 'Massive! The kids would love this.'),
-- Active space and science tour
(2, 5, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 12 MINUTE), 'I would like to learn more about how this was used.'),
(2, 2, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 5 MINUTE), 'Incredible that this actually went to space.'),
-- Active highlights tour
(3, 1, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 27 MINUTE), 'A bit crowded around this exhibit.'),
(3, 4, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 20 MINUTE), 'Beautiful brush strokes.'),
-- Completed space and science tour
(4, 5, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 23 HOUR), NULL),
(4, 2, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 22 HOUR), 'An amazing piece of space history.'),
(4, 3, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 21 HOUR), 'The scale is impressive.');
