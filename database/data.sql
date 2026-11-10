-- Insert 4 Objects
INSERT INTO Objects (name, description, extended_description) VALUES
('The Rosetta Stone', 'An ancient Egyptian granodiorite stele inscribed with three versions of a decree.', 'Discovered in 1799, it holds the key to understanding Egyptian hieroglyphs.'),
('Apollo 11 Command Module', 'The spacecraft that carried the first humans to the Moon.', 'Columbia is the only part of the Apollo 11 spacecraft that returned to Earth intact.'),
('Tyrannosaurus Rex Skeleton', 'A nearly complete fossilized skeleton of a T-Rex.', 'Excavated in the late 20th century, it stands over 12 feet tall at the hips.'),
('Starry Night', 'Famous oil on canvas painting by Vincent van Gogh.', 'Painted in June 1889, it depicts the view from the east-facing window of his asylum room.');

-- Insert random images for the objects (using generic placeholder image URLs)
INSERT INTO Objects_Images (object_id, image_url) VALUES
(1, 'https://picsum.photos/id/1015/400/300'),
(2, 'https://picsum.photos/id/1025/400/300'),
(3, 'https://picsum.photos/id/1035/400/300'),
(4, 'https://picsum.photos/id/1045/400/300'),
(4, 'https://picsum.photos/id/1055/400/300'); -- Adding a second image for object 4 just to show 1-to-many

-- Insert 1 Tour
INSERT INTO Tours (name, description) VALUES
('Museum Highlights Tour', 'A quick 30-minute walkthrough of the museum''s most famous artifacts and exhibits.');

-- Map the 4 Objects to the 1 Tour (Assigning positions 1 through 4)
INSERT INTO Tours_Objects (tour_id, position, object_id) VALUES
(1, 1, 1), -- Rosetta Stone is 1st
(1, 2, 4), -- Starry Night is 2nd
(1, 3, 2), -- Apollo 11 is 3rd
(1, 4, 3); -- T-Rex is 4th

-- Insert 3 Guest Users using standard 36-character UUIDs
INSERT INTO Users (id, is_guest) VALUES
('550e8400-e29b-41d4-a716-446655440000', 1),
('a3b8c4d2-f9e1-4589-b271-9c8d7e6f5a4b', 1),
('c8f9b2a1-e4d7-4632-9018-f2b3c4d5e6f7', 1);

-- Insert 3 Sessions for these users on Tour 1 (Museum Highlights Tour)
-- Using DATE_SUB to simulate sessions that started at different times in the past
INSERT INTO Sessions (user_id, created_at, active, tour_id, archetype) VALUES
-- Session 1: Completed session, started 2 hours ago
('550e8400-e29b-41d4-a716-446655440000', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 120 MINUTE), 0, 1, 'The Scholar'),

-- Session 2: Currently active session, started 15 minutes ago
('a3b8c4d2-f9e1-4589-b271-9c8d7e6f5a4b', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE), 1, 1, 'The Explorer'),

-- Session 3: Completed session, started 1 day ago
('c8f9b2a1-e4d7-4632-9018-f2b3c4d5e6f7', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY), 0, 1, 'The Casual Visitor');

-- Insert Stops for these sessions at various objects (1=Rosetta Stone, 2=Apollo 11, 3=T-Rex, 4=Starry Night)
INSERT INTO Stops (session_id, object_id, time_of_stop, feedback_text) VALUES
-- Stops for Session 1 (Completed the whole tour)
(1, 1, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 115 MINUTE), 'Fascinating to see the real translation key.'),
(1, 4, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 100 MINUTE), 'The colors are much more vibrant in person.'),
(1, 2, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 85 MINUTE), NULL),
(1, 3, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 70 MINUTE), 'Massive! The kids would love this.'),

-- Stops for Session 2 (Active session, only reached the first two objects so far)
(2, 1, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 12 MINUTE), 'A bit crowded around this exhibit.'),
(2, 4, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 5 MINUTE), 'Beautiful brush strokes.'),

-- Stops for Session 3 (Abandoned the tour after two stops)
(3, 1, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 23 HOUR), NULL),
(3, 2, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 22 HOUR), 'Incredible that this actually went to space!');