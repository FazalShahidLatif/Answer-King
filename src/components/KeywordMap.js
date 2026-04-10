import { useEffect, useRef } from '@wordpress/element';
import * as d3 from 'd3';

const KeywordMap = ({ data, rootName }) => {
    const svgRef = useRef();

    useEffect(() => {
        if (!data) return;

        // Transform data to D3 hierarchy format
        const hierarchyData = {
            name: rootName,
            children: Object.keys(data).map(key => ({
                name: key.charAt(0).toUpperCase() + key.slice(1),
                children: data[key].map(node => ({ 
                    name: node.name,
                    metrics: node.metrics 
                }))
            }))
        };

        const width = 1000;
        const radius = width / 2;

        // Color scale for Difficulty (Green -> Red)
        const colorScale = d3.scaleLinear()
            .domain([0, 50, 100])
            .range(['#22c55e', '#eab308', '#ef4444']);

        // Clear previous chart
        d3.select(svgRef.current).selectAll('*').remove();

        const svg = d3.select(svgRef.current)
            .attr('viewBox', [-radius, -radius, width, width])
            .append('g');

        const tree = d3.tree()
            .size([2 * Math.PI, radius - 150])
            .separation((a, b) => (a.parent === b.parent ? 1 : 2) / a.depth);

        const root = tree(d3.hierarchy(hierarchyData));

        // Links
        svg.append('g')
            .attr('fill', 'none')
            .attr('stroke', '#cbd5e1')
            .attr('stroke-opacity', 0.6)
            .attr('stroke-width', 1.5)
            .selectAll('path')
            .data(root.links())
            .join('path')
            .attr('d', d3.linkRadial()
                .angle(d => d.x)
                .radius(d => d.y));

        // Nodes
        const node = svg.append('g')
            .selectAll('g')
            .data(root.descendants())
            .join('g')
            .attr('transform', d => `
                rotate(${(d.x * 180 / Math.PI - 90)})
                translate(${d.y},0)
            `);

        node.append('circle')
            .attr('fill', d => {
                if (!d.depth) return '#0073aa'; // Root
                if (d.data.metrics) return colorScale(d.data.metrics.difficulty);
                return '#cbd5e1';
            })
            .attr('r', d => {
                if (!d.depth) return 10;
                if (d.data.metrics) return d3.scaleSqrt().domain([0, 15000]).range([3, 15])(d.data.metrics.volume);
                return 5;
            })
            .style('cursor', 'pointer')
            .append('title')
            .text(d => d.data.metrics ? `Vol: ${d.data.metrics.volume}\nDiff: ${d.data.metrics.difficulty}\nCPC: $${d.data.metrics.cpc}` : d.data.name);

        node.append('text')
            .attr('dy', '0.31em')
            .attr('x', d => d.x < Math.PI ? 10 : -10)
            .attr('text-anchor', d => d.x < Math.PI ? 'start' : 'end')
            .attr('transform', d => d.x >= Math.PI ? 'rotate(180)' : null)
            .text(d => d.data.name)
            .style('font-size', d => d.children ? '14px' : '10px')
            .style('font-weight', d => d.children ? 'bold' : 'normal')
            .style('fill', '#333')
            .clone(true).lower()
            .attr('stroke', 'white')
            .attr('stroke-width', 3);

        // Add zoom interaction
        d3.select(svgRef.current).call(d3.zoom().on('zoom', (event) => {
            svg.attr('transform', event.transform);
        }));

    }, [data, rootName]);

    return (
        <div className="ak-visualization-wrapper">
            <svg ref={svgRef} style={{ width: '100%', height: '600px', cursor: 'move' }}></svg>
        </div>
    );
};

export default KeywordMap;
