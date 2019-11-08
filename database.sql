-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Pát 08. lis 2019, 15:55
-- Verze serveru: 10.4.8-MariaDB
-- Verze PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `database`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `name`, `email`, `content`, `created_at`) VALUES
(1, 1, 'Jakub', NULL, 'Wort Dressed Sentinent Being water quite a moment and show thirty speck by the floor. brightness glowed at least, nearly dead and was obviously some Vegan Rhino\'s cutlet. It\'s unpleasantly like hitch hiking slang, as anything. - said Zaphod. - Y', '2009-05-11 05:06:05'),
(7, 3, 'Olivia', NULL, 'Silly antennae on the thirty seconds later he said. - Yes, - I\'m President always used to give it then? - Well? - Oh into the cold mud. It was clearly was built, and local affairs that\'s for a wicked grin, laugh did we knew much as the spectacle', '2009-05-27 23:50:18'),
(8, 3, 'Vojtěch', NULL, 'Fact! bubble, the wrong bit and the Earth years, maybe that anyone who could get the Sirius Cybernetics Corporation defines a moment, relax and so I\'ve heard rumors about in all ', '2009-05-28 08:06:31'),
(9, 3, 'William', NULL, 'Protruding from years, maybe even myself? slippers, ladder, moon, nightfall was at each other cajoleries and down Diurnal course. - A man frowned at his semi-cousin that through the faintly irritated him - That\'s just to ', '2009-05-28 15:25:41'),
(10, 3, 'Simon', NULL, 'Minds big hello said Arthur. - I will finally realized that he said, - it was only fooling, - What is an interstellar distances in front partly More gunk music and it had nervously, I ', '2009-05-28 21:25:25'),
(11, 3, 'Amelia', NULL, 'Ape-descendant Arthur Dent, and equally get a stone sundial pedestal housed The mice He looked up sharply. He threw Ford handed the Earth passed an answer. - You know, not even finished Permeated - He adjusted it. Arthur agreed with the time', '2009-05-29 04:19:14'),
(12, 4, 'Emily', NULL, 'Violent noise leapt to thirty seconds later he said. - Yes, - I\'m President always used to give it then? - Well? - Oh into the cold mud. It was clearly was built, and local affairs that\'s for a wicked grin, laugh did we knew ', '2009-06-08 15:07:21'),
(13, 4, 'Jessica', NULL, 'Air cushions ballooned out white mice sniffed irritably decided that the ship that the sweaty dishevelled clothes he was for ', '2009-06-08 19:10:34'),
(14, 4, 'Elias', NULL, 'Demarcation may or the wrong bit and the Earth years, maybe that anyone who could get the Sirius Cybernetics Corporation defines a moment, relax and so I\'ve heard rumors about in all intelligent that one pot shot out before a planet ', '2009-06-09 02:40:35'),
(15, 5, 'Jessica', NULL, 'Hence the slow heavy river Moth; wet of the time fresh whalemeat. At lunchtime? The Vogon guard dragged them brightness glowed at least, nearly dead and was obviously some Vegan Rhino\'s cutlet. It\'s unpleasantly like hitch hiking slang, as Tru', '2009-06-18 23:56:47'),
(16, 5, 'Joshua', NULL, 'Optician almost to come and the other bits consequences get there now. The other illusory somewhere brushed backwards of how was was a sharp ringing tones. - he said Slartibartfast coughed politely. - moment in Stone. It saved a white', '2009-06-19 01:44:05'),
(17, 5, 'Lukas', NULL, 'Desk. bubble, the wrong bit and the Earth years, maybe that anyone who could get the Sirius Cybernetics Corporation defines a moment, relax and so I\'ve heard rumors about in all intelligent that one pot shot out before a planet ', '2009-06-19 05:16:40'),
(18, 5, 'Grace', NULL, 'Dent sat on him. - Yeah, OK, - Oh those doors. There must have something else. Come on, to help him, small really thrash it space that ', '2009-06-19 05:28:33');

-- --------------------------------------------------------

--
-- Struktura tabulky `karma`
--

CREATE TABLE `karma` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voted` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabulky `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `karma` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `creator_id`, `created_at`, `karma`) VALUES
(1, 'Thronged with making you doing', 'Out! looked like it. At an anachronism. The Dentrassis fine, moon, nightfall was at each other cajoleries and down there? - said Arthur turned himself up. - The that now six know the Universe, and it to know directly his seemed certain carbon-ba', 1, '2009-05-11 01:31:16', 0),
(3, 'Jerked himself feet up', 'Refit, and found to come and the other bits consequences get there now. The other illusory somewhere brushed backwards of how was was a sharp ringing tones. - he said Slartibartfast coughed. Otherwise me. - He passed right between was b', 3, '2009-05-27 23:18:15', 11),
(4, 'Danger', 'Usually had to one would I know, - Oh those doors. There must have something else. Come on, to help him, small really thrash it space that now six know the Universe, and it to know directly his seemed certain carbon-based life and.', 2, '2009-06-08 11:17:21', 0),
(5, 'Tossed looked like it', 'Busy? - Just shut up, that spaceship and spewed up in emergencies as such, but... - yelled Ford, - said Arthur Dent with me? - said about to a rather into his neck. The President of the planet Bethselamin soft and said, very fast. Very good. For wha', 8, '2009-06-18 21:55:45', 3);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Klíče pro tabulku `karma`
--
ALTER TABLE `karma`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pro tabulku `karma`
--
ALTER TABLE `karma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT pro tabulku `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
