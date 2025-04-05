<?php

declare(strict_types=1);

/**
 * BigBlueButton open source conferencing system - https://www.bigbluebutton.org/.
 *
 * Copyright (c) 2016-2021 BigBlueButton Inc. and by respective authors (see below).
 *
 * This program is free software; you can redistribute it and/or modify it under the
 * terms of the GNU Lesser General Public License as published by the Free Software
 * Foundation; either version 3.0 of the License, or (at your option) any later
 * version.
 *
 * BigBlueButton is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with BigBlueButton; if not, see <http://www.gnu.org/licenses/>.
 */

namespace BigBlueButton\Core;

/* @deprecated Use \BigBlueButton\Enum\MeetingLayout instead */
final class MeetingLayout
{
    public const CUSTOM_LAYOUT = \BigBlueButton\Enum\MeetingLayout::CUSTOM_LAYOUT;
    public const SMART_LAYOUT = \BigBlueButton\Enum\MeetingLayout::SMART_LAYOUT;
    public const PRESENTATION_FOCUS = \BigBlueButton\Enum\MeetingLayout::PRESENTATION_FOCUS;
    public const VIDEO_FOCUS = \BigBlueButton\Enum\MeetingLayout::VIDEO_FOCUS;
}
