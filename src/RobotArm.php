<?php

namespace SalesFloor;

/**
 * Class RobotArm
 * @package SalesFloor
 */
class RobotArm
{
    private $fileName;
    private $stack      = [];
    private $positions  = [];
    private $commands   = [];

    /**
     * RobotArm constructor.
     * @param $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;

        $this->prepareRobot();
    }

    /**
     * Read the commands file and prepare the environment.
     */
    private function prepareRobot()
    {
        $file = fopen($this->fileName, 'r');

        while($command = fgets($file)) {
            $command = strtolower(trim($command));

            if (is_numeric($command) && $command > 0 && $command < 25) {
                $this->initStack((int) $command);
                continue;
            }

            if ($command === 'quit') {
                break;
            }

            $this->addCommand($command);
        }

        fclose($file);
    }

    /**
     * Initialize the stack.
     *
     * @param $countBlocks
     */
    private function initStack($countBlocks)
    {
        $this->positions = range(0, $countBlocks - 1);

        $this->stack = array_map(function($block){
            return "[$block]";
        }, $this->positions);
    }

    /**
     * Add commands.
     *
     * @param $command
     * @return bool
     */
    public function addCommand($command)
    {
        if (preg_match("/^(move|pile)\s+([0-9]+)\s+(onto|over)\s+([0-9]+)$/", $command, $return)) {
            list(, $type, $a, $direction, $b) = $return;

            if ($a >= count($this->stack) || $b >= count($this->stack)) {
                return false;
            }

            $function = sprintf("%s%s%s%s", $type, 'A', ucfirst($direction), 'B');

            $this->commands[] = [$function, $a, $b];
        }

        return true;
    }

    /**
     * Execute every command.
     *
     * @return array
     */
    public function exec()
    {
        foreach($this->commands as [$function, $a, $b]) {
            if ($a == $b || $this->positions[$a] == $this->positions[$b]) {
                continue;
            }

            $this->$function($a, $b);
        }

        return $this->stack;
    }

    /**
     * Move block A to block B.
     *
     * @param $a
     * @param $b
     */
    protected function moveAOntoB($a, $b)
    {
        $this->moveBlocksOnTopToInicialPosition($b);
        $this->moveAOverB($a, $b);
    }

    /**
     * Move block A over block B.
     *
     * @param $a
     * @param $b
     */
    protected function moveAOverB($a, $b)
    {
        $this->moveBlocksOnTopToInicialPosition($a);
        $this->removeBlock($a, "[$a]");
        $this->appendBlock($b, "[$a]");
        $this->setPosition($a, $this->getPosition($b));
    }

    /**
     * Pile block A onto block B.
     *
     * @param $a
     * @param $b
     */
    protected function pileAOntoB($a, $b)
    {
        $this->moveBlocksOnTopToInicialPosition($b);
        $this->pileAOverB($a, $b);
    }

    /**
     * Pile block A over block B.
     *
     * @param $a
     * @param $b
     */
    protected function pileAOverB($a, $b)
    {
        $stack = $this->shiftStack($a, "[$a]");

        $this->appendBlock($b, trim($stack));
        $this->setBlocksPosition($stack, $b);
    }

    /**
     * Move the above blocks to starting position.
     *
     * @param $index
     */
    protected function moveBlocksOnTopToInicialPosition($index)
    {
        $block    = "[$index]";
        $stack    = $this->getStack($block);
        $onTop    = substr($stack, strpos($stack, $block) + strlen($block));
        $arrOnTop = explode(' ', trim($onTop));

        $this->setStack($block, str_replace($onTop, '', $this->getStack($block)));

        foreach($arrOnTop as $block) {
            if (empty($block)) {
                continue;
            }

            $this->setPosition($block, $block);

            $stack = $this->getStack($block);

            $this->setStack($block, $block . (!empty($stack) ? ' ' : '') . $stack);
        }
    }

    /**
     * Set position of the blocks stack.
     *
     * @param $stack
     * @param $blockPosition
     */
    private function setBlocksPosition($stack, $blockPosition)
    {
        $blocks = explode(' ', trim($stack));

        foreach($blocks as $block) {
            if (empty($block)) {
                continue;
            }

            $this->setPosition($block, $this->getPosition($blockPosition));
        }
    }

    /**
     * Get position.
     *
     * @param $block
     * @return mixed
     */
    private function getPosition($block)
    {
        return $this->positions[trim($block, '[]')];
    }

    /**
     * Set position.
     *
     * @param $index
     * @param $block
     */
    private function setPosition($index, $block)
    {
        $this->positions[trim($index, '[]')] = trim($block, '[]');
    }

    /**
     * Get stack.
     *
     * @param $block
     * @return mixed
     */
    private function getStack($block)
    {
        return $this->stack[$this->positions[trim($block, '[]')]];
    }

    /**
     * Set stack.
     *
     * @param $block
     * @param $value
     */
    private function setStack($block, $value)
    {
        $this->stack[$this->positions[trim($block, '[]')]] = $value;
    }

    /**
     * Remove block from stack.
     *
     * @param $indexStack
     * @param $block
     */
    private function removeBlock($indexStack, $block)
    {
        $stack = $this->getStack($indexStack);

        $this->setStack($indexStack, str_replace([$block, " $block"], '', $stack));
    }

    /**
     * Append a block over the stack.
     *
     * @param $indexStack
     * @param $block
     */
    private function appendBlock($indexStack, $block)
    {
        $stack = $this->getStack($indexStack);

        $this->setStack($indexStack, $stack . (!empty($stack) ? ' ' : '') . $block);
    }

    /**
     * Remove a block stack from original position and return it.
     *
     * @param $indexStack
     * @param $block
     * @return bool|string
     */
    private function shiftStack($indexStack, $block)
    {
        $stack           = $this->getStack($indexStack);
        $blockAandOverIt = substr($stack, strpos($stack, $block));

        $this->setStack($indexStack, trim(str_replace($blockAandOverIt, '', $stack)));

        return $blockAandOverIt;
    }
}