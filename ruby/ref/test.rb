
class Cell
    def initialize(value)
        @value = value
    end

    def value(v)
        @value = v
    end

    def display
        @value
    end
end

c = Cell.new("Foo")
arr = [c]

p(arr.map(&:display).include?("Foo"))
# c.value("bar")

p(arr)