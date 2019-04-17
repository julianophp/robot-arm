# robot-arm

## Installation

``` bash
$ composer install
```

## Usage

```bash
$ composer run [file_name]
```

Replace [file_name] with a valid commands file name.  

## Testing

``` bash
$ composer test
```

## Example

```bash
$ composer run example/commands.txt
```

Below there is an example of valid commands:

```
10
move 9 onto 1
move 8 over 1
move 7 over 1
move 6 over 1
pile 8 over 6
pile 8 over 5
move 2 over 1
move 4 over 9
quit
```

