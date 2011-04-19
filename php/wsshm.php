<?php

/*****************************************************************************
//
// Module for working with Shared Memory in PHP - Makes it a little easier
//
// Copyright (C) 2004  L. Petersen, Weird Silence, www.weirdsilence.net
// 
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//
*****************************************************************************/

    class wsShm
    {
        var $key = NULL;
        var $shm_id = NULL;
        var $pos = 0;
        var $dbg = 0;
        
        function str2long($text)
        {
            return (ord($text[0]) << 24) | (ord($text[1]) << 16) | (ord($text[2]) << 8) | ord($text[3]);
        }

        function open($key,$mode,$size = 0)
        {
            $this->key = $key;
            if ($mode == 'rb')
            {
                if ($this->shm_id = @shmop_open($this->key, 'a', 0, 0))
                {
                    return $this->shm_id;
                }
                else
                {
                    return 0;
                }
            }
            elseif ($mode == 'wb')
            {
                if ($size == 0)
                {
                    die("Don't create shared memory with size 0...");
                }
                if ($id = @shmop_open($this->key, 'w', 0, 0))
                {
                    @shmop_delete($id);
                    @shmop_close($id);
                }
                if ($this->shm_id = shmop_open($this->key, 'n', 0644, $size))
                {
                    return $this->shm_id;
                }
                else
                {
                    return 0;
                }
            }
            elseif ($mode == 'ab')
            {
                if ($this->shm_id = @shmop_open($this->key, 'w', 0644, $size))
                {
                    return $this->shm_id;
                }
                else
                {
                    return 0;
                }
            }
            $this->pos = 0;
        }

        function seek($position)
        {
            if ($this->shm_id)
            {
                $this->pos = $position;
                return 1;
            }
            else
            {
                return 0;
            }
        }

        function readLong()
        {
            $tmp = $this->read(4);
            return ord($tmp[3]) << 24 | ord($tmp[2]) << 16 | ord($tmp[1]) << 8 | ord($tmp[0]);
        }

        function readShort()
        {
            $tmp = $this->read(2);
            return ord($tmp[1]) << 8 | ord($tmp[0]);
        }

        function readByte()
        {
            $tmp = $this->read(1);
            return ord($tmp[0]);
        }

        function read($count)
        {
            if ($this->shm_id)
            {
                $pos = $this->pos;
                $this->pos += $count;
                return @shmop_read($this->shm_id, $pos, $count);
            }
            else
            {
                return 0;
            }
        }

        function write($data)
        {
            if ($this->shm_id)
            {
                $pos = $this->pos;
                $this->pos += strlen($data);
                return @shmop_write($this->shm_id, $data, $pos);
            }
            else
            {
                return 0;
            }
        }

        function close()
        {
            if ($this->shm_id)
            {
                $this->shm_id = NULL;
                return @shmop_close($this->shm_id);
            }
            else
            {
                return 0;
            }
        }

        function delete()
        {
            if ($this->shm_id)
            {
                $id = $this->shm_id;
                $this->shm_id = NULL;
                return @shmop_delete($id);
            }
            else
            {
                return 0;
            }
        }
    
        function size()
        {
            if ($this->shm_id)
            {
                return @shmop_size($this->shm_id);
            }
            else
            {
                return 0;
            }
        }
    
    }

?>
